<style type="text/css">
  .multi_layout{margin:0;background: #fff}
  .multi_layout .card{margin-bottom:0;border-radius: 0;}
  .multi_layout p, .multi_layout ul:not(.list-unstyled), .multi_layout ol{line-height: 15px;}
 /* .multi_layout .list-group li{padding: 25px 10px 12px 25px;}*/
  .multi_layout{border:.5px solid #dee2e6;}
  .multi_layout .collef,.multi_layout .colmid{padding-left: 0px; padding-right: 0px;border-right: .6px solid #dee2e6;border-bottom: .6px solid #dee2e6;}
  .multi_layout .colmid .card-icon{border:.5px solid #dee2e6;}
  .multi_layout .colmid .card-icon i{font-size:30px !important;}
  .multi_layout .main_card{min-height: 400px;}
  .multi_layout .collef .makeScroll{max-height:430px;overflow:auto;}
/*  .multi_layout .list-group .list-group-item{border-radius: 0;border:.5px solid #dee2e6;border-left:none;border-right:none;z-index: 0;}
  .multi_layout .list-group .list-group-item:first-child{border-top:none;}
  .multi_layout .list-group .list-group-item:last-child{border-bottom:none;}
  .multi_layout .list-group .list-group-item.active{border:.5px solid #6777EF;}*/
  .multi_layout .mCSB_inside > .mCSB_container{margin-right: 0;}
  .multi_layout .card-statistic-1{border:.5px solid #dee2e6;border-radius: 4px;}
  .multi_layout h6.page_name{font-size: 14px;}
  .multi_layout .card .card-header input{max-width: 100% !important;}
  .multi_layout .card-primary{margin-top: 35px;margin-bottom: 15px;}
  .multi_layout .product-details .product-name{font-size: 12px;}
  .multi_layout .margin-top-50 {margin-top: 70px;}
  .multi_layout .waiting {height: 100%;width:100%;display: table;}
  .multi_layout .waiting i{font-size:60px;display: table-cell; vertical-align: middle;padding:10px 0;}
  .waiting {padding-top: 200px;}
  .check_box{position: absolute !important;top: 0 !important;right: 0 !important;margin: 3px;}
  .check_box_background{position: absolute;height: 60px;width: 60px;top: 0;right: 0;font-size: 13px;}
  .profile-widget { margin-top: 0;}
  .profile-widget .profile-widget-items:after {content: ' ';position: absolute;bottom: 0;left: 0px;right: 0;height: 1px;background-color: #f2f2f2;}
  .profile-widget .profile-widget-items:before {content: ' ';position: absolute;top: 0;left: 0px;right: 0;height: 1px;background-color: #f2f2f2;}
  .profile-widget .profile-widget-items .profile-widget-item {flex: 1;text-align: center;padding: 10px 0;}
  .article .article-header {overflow: unset !important;}
  .description_info {padding: 20px;line-height: 17px;font-size: 13px;margin: 0;}
  .option_dropdown {position: absolute;top: 0;left: 0;height: 20px;width: 22px;background-color: #f7fefe;border-radius: 24%;padding-top: 0px;margin-top: 3px;margin-left: 3px;border: 1px solid #4e6e7e;}
  .video_option_background{position: absolute;height: 60px;width: 60px;top: 0;left: 0;}
  .selectric .label {min-height: 0 !important;}
  .opt_btn{border-radius: 30px !important;padding-left: 25px !important;padding-right: 25px !important;}
  .generic_message_block textarea{height: 100px !important;}
  .filter_message_block textarea{height: 100px !important;margin-bottom: 30px;}
  .single_card .card-body .form-group{margin-bottom: 10px;}
  .single_card .card-body{padding-bottom: 0 !important;}
  .bootstrap-tagsinput{height: 100px !important;}
  .profile-widget .profile-widget-items .profile-widget-item .profile-widget-item-value {font-weight: 300;font-size: 13px;
  }
  .padding-0{padding: 0px;}
  .bck_clr{background: #ffffff!important;}

  .mt-136{margin-top: 136px!important;}
    .ajax-file-upload{
      bottom: 12px;
  }
</style>




<section class="section">
  <div class="section-header">
    <h1><i class="fas fa-globe"></i> <?php echo $page_title;?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><a href="<?php echo base_url('menu_loader/analysis_tools') ?>"><?php echo $this->lang->line("Analysis Tools");?></a></div>
      <div class="breadcrumb-item"><?php echo $page_title;?></div>
    </div>
  </div>
</section>
  

<div class="row multi_layout">

  <div class="col-12 col-md-5 col-lg-5 collef">
    <div class="card main_card">
      <div class="card-header">
        <h4><i class="fas fa-info-circle"></i> <?php echo $this->lang->line('Info'); ?></h4>
      </div>
      <form  method="POST" enctype="multipart/form-data"  id="new_search_form">
        


        <div class="card-body">

          <div class="form-group">
              <label class="form-label"> <?php echo $this->lang->line("Domain"); ?> <code>*</code> <a href="#" data-placement="top" data-toggle="popover" data-trigger="focus" title="" data-content="Put your domain names comma separated" data-original-title="Domain"><i class="fa fa-info-circle"></i> </a></label>
             
              <textarea id="domain_name" name="domain_name" class="form-control" style="width:100%;min-height: 140px;" rows="10"></textarea>
          </div>

        </div>

        <div class="card-footer bg-whitesmoke mt-136">

            <button type="button"  id="new_search_button" class="btn btn-primary "><?php echo $this->lang->line("Analysis"); ?></button>
            <button class="btn btn-secondary btn-md float-right" onclick="goBack('ip/ip_canonical_check')" type="button"><i class="fa fa-remove"></i> <?php echo $this->lang->line('Cancel'); ?></button>
          
    

        </div>

      </form>
    </div>          
  </div>

  <div class="col-12 col-md-7 col-lg-7 colmid">
    <div id="custom_spinner"></div>
    <div id="unique_per">
      
    </div>
    <div id="middle_column_content" style="background: #ffffff!important;">

      <div class="card">
        <div class="card-header">
          <h4> <i class="fas fa-globe"></i> <?php echo $this->lang->line('DNS Information'); ?></h4>
          
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-6 col-lg-12 bck_clr" id="nodata">

        <div class="empty-state">
          <img class="img-fluid" src="<?php echo base_url("assets/img/drawkit/revenue-graph-colour.svg"); ?>" style="height: 300px" alt="image">
        

        </div>

      </div>
    </div>
  </div>
</div>

<script>       
  var base_url="<?php echo site_url(); ?>";
</script>



<script type="text/javascript">

  $("document").ready(function(){

    $(document).on('click', '#new_search_button', function(event) {
      event.preventDefault();

      var domain_name=$("#domain_name").val();

      if (domain_name == '') {
        swal("<?php echo $this->lang->line('Error'); ?>", "<?php echo $this->lang->line('Please Enter Domain Name'); ?>", 'error');
        return false;
      }
      
      $('#middle_column_content').html("");
      $("#new_search_button").addClass('btn-progress');
      $("#custom_spinner").html('<div class="text-center waiting"><i class="fas fa-spinner fa-spin blue text-center"></i></div><br/><p class="text-center"><?php echo $this->lang->line('Please wait for while...'); ?></p>');


      $.ajax({
        url:base_url+'dns_info/dns_info_action',
        type:'POST',
        data:{domain_name:domain_name},
        dataType:'json',
        success:function(response){ 
          $("#new_search_button").removeClass('btn-progress');
          $("#custom_spinner").html("");
          $("#middle_column_content").html(response.url_lists);

          
        }

      });
        
    });

    $(document).on('click','.details',function(event){
      event.preventDefault();

        let single_details = $(this).data('details');
        single_details = JSON.stringify(single_details);
        single_details  = JSON.parse(single_details);
        
        var html = '<div class="table-responsive table-invoice"><table class="table table-hover table-striped"><tbody><tr><th><?php echo $this->lang->line('Type'); ?></th><th><?php echo $this->lang->line('Host'); ?></th><th><?php echo $this->lang->line('IP/Target'); ?></th><th><?php echo $this->lang->line('Class'); ?></th><th><?php echo $this->lang->line('TTL'); ?></th></tr>';
        if (Array.isArray(single_details)) {
          single_details.forEach(function(single){
             if (single.ip)
                var ip = single.ip;
              else
                var ip = single.target;
             html += '<tr><td>'+single.type+'</td><td>'+single.host+'</td><td>'+ip+'</td><td>'+single.class+'</td><td>'+single.ttl+'</td></tr></tbody>';
          })
        }
        html += '</table></div>';

        $('#who_is_download_selected').modal();
        $("#total_download_selected").html(html);

    })



  });  

</script>

<div class="modal fade show" id="who_is_download_selected">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="background: #fefefe;">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-server"></i> <?php echo $this->lang->line('DNS Information'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div id="custom_spinner"></div>
      <div class="modal-body text-center" id="total_download_selected"> 


      </div>
      
    </div>
  </div>
</div>

