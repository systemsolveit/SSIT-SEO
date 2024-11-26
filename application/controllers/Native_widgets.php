<?php

require_once("Home.php"); // loading home controller

/**
* @category controller
* class Admin
*/

class Native_widgets extends Home
{

    public $user_id;    

    /**
    * load constructor
    * @access public
    * @return void
    */
    
    public function __construct()
    {
        parent::__construct();
        
        $this->load->helper(array('form'));
        $this->load->library('upload');
        $this->load->library('Web_common_report');
        $this->upload_path = realpath(APPPATH . '../upload');
        $this->user_id = $this->session->userdata('user_id');
        set_time_limit(0);
        
    }


    public function index(){
        $this->get_widget();      
    }


    public function get_widget()
    {        
        $user_id = $this->user_id;

        $domain_list_id = $this->input->post("domain_list_id",true);
        $where = [];
        $where['where'] = array('user_id'=>$user_id);
        $domain_name = $this->basic->get_data('visitor_analysis_domain_list',$where,$select=array('id','domain_name','domain_code'));
        $data['domain_name_array'] = $domain_name;
        
        $data['body'] = 'native_widgets/widgets';
        $data['page_title'] = $this->lang->line("native widget");

        if($domain_list_id !='' && $this->basic->is_exist('visitor_analysis_domain_list',array("id"=>$domain_list_id,"user_id"=>$user_id))) {
            redirect("native_widgets/get_widget/".$domain_list_id, "location");
        }
        else {
            $this->_viewcontroller($data);
        }
    }

    public function public_content_overview_data($domain_code='')
    {        
        $to_date = date("Y-m-d");
        $from_date = date("Y-m-d",strtotime("$to_date-30 days"));

        $to_date = $to_date." 23:59:59";
        $from_date = $from_date." 00:00:00";
        $data = [];
        $data['from_date'] = date("d-M-y",strtotime($from_date));
        $data['to_date'] = date("d-M-y",strtotime($to_date));
        $info = $this->basic->get_data('visitor_analysis_domain_list',['where'=>['domain_code'=>$domain_code]],['id']);
        if(!empty($info))
        {
            $domain_list_id = $info[0]['id'];
            $where = array();
            $where['where'] = array(
                "date_time >=" => $from_date,
                "date_time <=" => $to_date,
                "domain_list_id" => $domain_list_id
                );

            $table = "visitor_analysis_domain_list_data";

            $select = array("count(id) as total_view","visit_url");
            $content_overview_data = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start=NULL,$order_by='total_view desc',$group_by='visit_url');
            $total_view = 0;
            foreach($content_overview_data as $value){
                $total_view = $total_view+$value['total_view'];
            }

            $data['total_view'] = $total_view;
            $data['content_overview_data'] = $content_overview_data;
            $data['data_found'] = 'yes';
        }
        else
            $data['data_found'] = 'no';

        $this->load->view("native_widgets/widget_for_content_overview", $data);

    }

    public function public_traffic_source_data($domain_code = '')
    {
        // Fetch domain details
        $info = $this->basic->get_data('visitor_analysis_domain_list', ['where' => ['domain_code' => $domain_code]], ['id']);
        if (empty($info)) {
            $data['data_found'] = 'no';
            $this->load->view("native_widgets/widget_for_overview", $data);
            return;
        }
    
        $domain_list_id = $info[0]['id'];
        $to_date = date("Y-m-d 23:59:59");
        $from_date = date("Y-m-d 00:00:00", strtotime("-30 days"));
    
        // Query conditions
        $where = [
            'where' => [
                "date_time >=" => $from_date,
                "date_time <=" => $to_date,
                "domain_list_id" => $domain_list_id,
            ],
        ];
    
        $table = "visitor_analysis_domain_list_data";
    
        // Get total page views and unique visitors
        $total_page_view = $this->basic->get_data($table, $where);
        $total_unique_visitor = $this->basic->get_data($table, $where, [], '', '', '', '', 'cookie_value');
    
        // Get unique sessions and bounce rate data
        $select = ["count(id) as session_number", "last_scroll_time", "last_engagement_time"];
        $total_unique_session = $this->basic->get_data($table, $where, $select, '', '', '', '', 'session_value');
    
        // Calculate bounce rate
        $bounce = 0;
        $no_bounce = 0;
    
        foreach ($total_unique_session as $value) {
            if ($value['session_number'] > 1) {
                $no_bounce++;
            } elseif ($value['session_number'] == 1) {
                if ($value['last_scroll_time'] == "0000-00-00 00:00:00" && $value['last_engagement_time'] == "0000-00-00 00:00:00") {
                    $bounce++;
                } else {
                    $no_bounce++;
                }
            }
        }
    
        $total_sessions = $bounce + $no_bounce;
        $bounce_rate = $total_sessions > 0 ? number_format(($bounce * 100) / $total_sessions, 2) : 0;
    
        // Calculate average stay time
        $select = ["date_time as stay_from", "last_engagement_time", "last_scroll_time"];
        $stay_time_info = $this->basic->get_data($table, $where, $select);
    
        $total_stay_time = 0;
        foreach ($stay_time_info as $value) {
            $start_time = strtotime($value['stay_from']);
            $engagement_time = strtotime($value['last_engagement_time']);
            $scroll_time = strtotime($value['last_scroll_time']);
    
            if ($engagement_time == 0 && $scroll_time == 0) {
                continue;
            }
    
            $end_time = max($scroll_time, $engagement_time);
            $total_stay_time += ($end_time > 0) ? ($end_time - $start_time) : 0;
        }
    
        $average_stay_time = !empty($total_unique_session) ? $total_stay_time / count($total_unique_session) : 0;
    
        // Format average stay time
        $hours = (int) floor($average_stay_time / 3600);
        $minutes = (int) floor(($average_stay_time % 3600) / 60);
        $seconds = (int) $average_stay_time % 60;
    
        // Prepare data for the view
        $data = [
            'total_page_view' => number_format(count($total_page_view)),
            'total_unique_visitro' => number_format(count($total_unique_visitor)),
            'average_visit' => count($total_unique_visitor) > 0
                ? number_format(count($total_page_view) / count($total_unique_visitor), 2)
                : number_format(count($total_page_view)),
            'average_stay_time' => sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds),
            'bounce_rate' => $bounce_rate,
            'data_found' => 'yes',
        ];
    
        $this->load->view("native_widgets/widget_for_overview", $data);
    }
    
	
	public function public_country_report_data($domain_code='')
	{
        $info = $this->basic->get_data('visitor_analysis_domain_list',['where'=>['domain_code'=>$domain_code]],['id']);
        if(!empty($info))
        {
            $domain_list_id = $info[0]['id'];
            $to_date = date("Y-m-d");
            $from_date = date("Y-m-d",strtotime("$to_date-30 days"));        

            $to_date = $to_date." 23:59:59";
            $from_date = $from_date." 00:00:00";
            
            $where = array();
            $where['where'] = array(
                "date_time >=" => $from_date,
                "date_time <=" => $to_date,
                "domain_list_id" => $domain_list_id
                );

            $table = "visitor_analysis_domain_list_data";
            $select = array('country',"GROUP_CONCAT(is_new SEPARATOR ',') as new_user");
            $country_name = $this->basic->get_data($table,$where,$select,$join='',$limit='',$start=NULL,$order_by='',$group_by='country');
       
            $i = 0;
            $country_report = array();
            $a = array('Country','New Visitor');
            $country_report[$i] = $a;
            foreach($country_name as $value){
                $new_users = array();
                $i++;
                $new_users = explode(',', $value['new_user']);
                $new_users = array_filter($new_users);
                $new_users = array_values($new_users);
                $new_users = count($new_users);
                $temp = array();
                $temp[] = $value['country'];
                $temp[] = $new_users;
                $country_report[$i] = $temp;
            }
            $data['country_graph_data'] = json_encode($country_report);
            $data['data_found'] = 'yes';
        }
        else
            $data['data_found'] = 'no';

		$this->load->view("native_widgets/widget_for_country_report", $data);	
	}

}