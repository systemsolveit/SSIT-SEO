<?php
class cWhois {
    var $domainname;
    var $whoisContent;
    var $detailWhois = true;
    var $error = array();
    var $debug = false;
    var $whoisServer = '';
    var $defaultTimeZone;

    function __construct($domain = '') {
        return $this->cWhois($domain);
    }

    function cWhois($domain) {
        $this->error_reporting_status = error_reporting();
        error_reporting($this->error_reporting_status & ~E_WARNING);
        if (!empty($domain)) {
            $this->setDomain($domain);
            return $this->whois();
        }
    }

    function setDomain($domain) {
        $domain = $this->cleanDomain($domain);
        if (!empty($domain)) {
            $this->domainname = $domain;
            return true;
        }
        return false;
    }

    function setError($errorMsg) {
        $this->error[] = $errorMsg;
        return false;
    }

    function getError() {
        return $this->error;
    }

    function cleanDomain($domain) {
        $domain = str_replace(array('https://', 'http://', 'www.', '//', ' '), '', $domain);
        $domain = trim($domain);
        return $domain;
    }

    function getTld() {
        $domain = explode('.', $this->domainname);
        unset($domain[0]);
        return implode('.', $domain);
    }

    function whois() {
        $domain = $this->domainname;

        if (empty($domain)) {
            return $this->setError('Domain is not defined.');
        }

        $this->whoisServer = $this->getWhoisServer($this->getTld());

        // If the initial WHOIS server is not found, fallback to 'whois.iana.org'.
        if ($this->whoisServer == '' || $this->whoisServer == 'whois.iana.org') {
            $response = $this->getSocketResult($domain, 'whois.iana.org');

            // Check for redirect to a specific WHOIS server within the response.
            if (preg_match('/whois:[\s]*(.*?)\n/i', $response, $matches)) {
                $this->whoisServer = trim($matches[1]);
            } else {
                return $response; // Return the IANA response if no specific server found.
            }
        }

        // Fetch the actual WHOIS data.
        $response = $this->getSocketResult($domain, $this->whoisServer);

        // If there is a final WHOIS server in the response, get data from it.
        $lastWhoisServer = $this->parseWhoisServer($this->parseWhois(strtolower($response)));
        if (!empty($lastWhoisServer) && $this->whoisServer != $lastWhoisServer) {
            $this->whoisServer = $lastWhoisServer;
            $response = $this->getSocketResult($domain, $lastWhoisServer);
        }

        // Final WHOIS data parsing
        if ($this->detailWhois) {
            $parsedData = $this->parseWhois($response);
            if (isset($parsedData['Whois Server'])) {
                $this->whoisServer = $parsedData['Whois Server'];
                $response = $this->getSocketResult($domain, $this->whoisServer);
            }
        }

        if ($response) {
            $this->whoisContent = $response;
            return $response;
        }

        return $this->setError('Failed to retrieve WHOIS information.');
    }

    function getSocketResult($domain, $server) {
        $reConCount = 2; // Reconnection count
        while ($reConCount > 0) {
            $con = @fsockopen($server, 43, $errno, $errstr, 3);
            if (!$con) {
                $this->setError("$server not connected! Error: $errstr ($errno)");
                if ($reConCount == 1) {
                    return "$server not connected! Error: $errstr ($errno)";
                }
            } else {
                // Successfully connected
                fputs($con, $domain . "\n");

                $response = '';
                while (!feof($con)) {
                    $response .= fgets($con, 128);
                }
                fclose($con);

                // Clean up WHOIS comments and return the response
                return preg_replace("/%.*\n/", "", $response);
            }
            $reConCount--;
            sleep(1); // Delay before retrying
        }
        return false;
    }

    function parseWhoisServer($data) {
        $whoisServer = '';
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                if (strstr($k, 'whois server')) {
                    $whoisServer = $v;
                    break;
                }
            }
        } else if (is_string($data)) {
            preg_match_all('/whois server: (.*?)\n/sim', $data, $result, PREG_PATTERN_ORDER);
            $whoisServer = $result[1][0] ?? '';
        }
        return $whoisServer;
    }

function parseWhois($whoisText = '') {
    if (empty($whoisText)) {
        $whoisText = $this->whoisContent;
    }

    if (is_string($whoisText)) {
        // Clean up WHOIS text by removing unnecessary characters
        $whoisText = str_replace(array("\r", "<<<", ">>>"), "", $whoisText);
        $rows = explode("\n", $whoisText);
        $parsed = array();

        foreach ($rows as $row) {
            // Standard key-value extraction (handles "key: value" format)
            if ((strstr($row, '://') && substr_count($row, ":") > 1) || (!strstr($row, '://') && substr_count($row, ":") > 0)) {
                if (strlen($row) <= 100 && preg_match('/[\s]{0,}(.*?)[\s]{0,}:(.*?)$/sim', $row, $result)) {
                    $key = trim($result[1]);
                    $val = trim($result[2]);

                    // Store the parsed data in an associative array
                    if (!isset($parsed[$key])) {
                        $parsed[$key] = $val;
                    } else {
                        if (!is_array($parsed[$key])) {
                            $parsed[$key] = array($parsed[$key]);
                        }
                        if ($val !== '') {
                            $parsed[$key][] = $val;
                        }
                    }
                }
            }
            // Special case: Some WHOIS servers provide data without colons (e.g., "Name Server ns1.example.com")
            else if (preg_match('/^(Name Server|Domain Name|Registrar|Creation Date|Registry Expiry Date|Updated Date|Registrar URL)\s+(.*?)$/i', $row, $result)) {
                $key = trim($result[1]);
                $val = trim($result[2]);

                if (!isset($parsed[$key])) {
                    $parsed[$key] = $val;
                } else {
                    if (!is_array($parsed[$key])) {
                        $parsed[$key] = array($parsed[$key]);
                    }
                    $parsed[$key][] = $val;
                }
            }
        }

        // Additional cleanup for common fields to standardize names
        if (isset($parsed['Creation Date']) && !isset($parsed['Created At'])) {
            $parsed['Created At'] = $parsed['Creation Date'];
        }
        if (isset($parsed['Registry Expiry Date']) && !isset($parsed['Expire At'])) {
            $parsed['Expire At'] = $parsed['Registry Expiry Date'];
        }
        if (isset($parsed['Updated Date']) && !isset($parsed['Changed At'])) {
            $parsed['Changed At'] = $parsed['Updated Date'];
        }

        return $parsed;
    }
    return false;
}



    function __destruct() {
        if ($this->debug && count($this->error) > 0) {
            echo "ERROR:\n<pre>" . var_export($this->error, true);
        }
        error_reporting($this->error_reporting_status);
    }

function getWhoisServer($tld) {
    $whoisServers = array(
        'com' => 'whois.verisign-grs.com',
        'net' => 'whois.verisign-grs.com',
        'org' => 'whois.pir.org',
        'info' => 'whois.afilias.net',
        'biz' => 'whois.neulevel.biz',
        'us' => 'whois.nic.us',
        'co.uk' => 'whois.nic.uk',
        'de' => 'whois.denic.de',
        'fr' => 'whois.nic.fr',
        'au' => 'whois.aunic.net',
        // Add more TLD-specific servers as needed
    );

    return $whoisServers[$tld] ?? 'whois.iana.org';
}


    function getRegistrar() {
        if (!empty($this->whoisContent)) {
            preg_match_all('/Registrar:\s(.*?)\n/sx', $this->whoisContent, $result, PREG_PATTERN_ORDER);
            return trim($result[1][0] ?? '');
        }
        return false;
    }

    function getDate($dateType = 'expire') {
        $arrayWhois = $this->parseWhois();
        if ($dateType == 'expire' && isset($arrayWhois['Registrar Registration Expiration Date'])) {
            return $arrayWhois['Registrar Registration Expiration Date'];
        } elseif ($dateType == 'create' && isset($arrayWhois['Creation Date'])) {
            return $arrayWhois['Creation Date'];
        } elseif ($dateType == 'update' && isset($arrayWhois['Updated Date'])) {
            return $arrayWhois['Updated Date'];
        }
        return false;
    }

    function getUnixTime($dateType = 'expire') {
        $this->defaultTimeZone = date_default_timezone_get();
        date_default_timezone_set('Europe/Istanbul');

        $date = $this->getDate($dateType);
        $result = $date ? strtotime(trim(str_replace(array('T', 'Z'), ' ', $date))) : false;

        date_default_timezone_set($this->defaultTimeZone);
        return $result;
    }

    function getWhoisProp($properties) {
        $arrayWhois = $this->parseWhois();
        return $arrayWhois[$properties] ?? false;
    }
}
?>
