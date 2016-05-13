
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Single Window</title>
 
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

</head>
<body>
    <div id="container">
    <h1>Member Address</h1>
        <div class="row">
            <div class="box col-md-12">
                <div class="box-inner">
                       <?php echo form_open('v1/member/address'); ?>
         <!--        <form action="#" id="form" class="form-horizontal"> -->
                    <input type="" name="member_seq"  value="<?php echo $member_seq ?>"/> 
<!--                    <input name="address_seq" type="" value ="<?php  echo (isset($member->seq) ? $member->seq : "") ?>">-->
                  
                    <input name= 'action' type="" placeholder = "action"> 
					<input type="text" name="token" placeholder = "token">
                    <div class ="form-group">
                        <label class ="control-label col-md-4">Alias *</label>
                        <div class ="col-md-8">
                            <input class="form-control" name="alias" type="text" maxlength="25" 
                            value ="<?php  echo (isset($member->alias) ? $member->alias : "") ?>" >
                        </div>
                    </div>
                    <div class ="form-group">
                        <label class ="control-label col-md-4">Penerima *</label>
                        <div class ="col-md-8">
                            <input class="form-control" name="pic_name" type="text" value ="<?php  echo (isset($member->pic_name) ? $member->pic_name : "") ?>">
                        </div>
                    </div>
                    <div class ="form-group">
                        <label class ="control-label col-md-4">Alamat *</label>
                        <div class ="col-md-8">
                            <input class="form-control" name="address" type="text" value ="<?php  echo (isset($member->address) ? $member->address : "") ?>">
                        </div>
                    </div>
                    <div class ="form-group">
                        <label class ="control-label col-md-4">No. Telp</label>
                        <div class ="col-md-8">
                            <input class="form-control" name="phone_no" type="text" value="<?php  echo (isset($member->phone_no) ? $member->phone_no : "") ?>">
                        </div>
                    </div>
<?php /*                    <div class ="form-group">
                        <label class ="control-label col-md-4">Propinsi *</label>
                        <div class ="col-md-8">
                            <select id="f_province" name="f_province">
                              
                              <?php 
                               foreach($provinces as $province){
                                    echo '<option value="' . $province->seq . '">' . $province->name . '</option>';
                                }
                                ?>
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class ="form-group">
                        <label for="f_city">Kota</label>
                        <div class ="col-md-8">
                            <select id="f_city" name="f_city" id="f_city_label"> 
                           
                            <?php 
                               echo '<option value="' . $cityQ->seq . '" >' . $cityQ->name . '</option>';
                                
                      
                                ?>
                                 <option value=""></option>
                            </select>
                        </div>
                    </div>
                      <div class ="form-group">
                        <label for="district_seq">Kecamatan </label>
                        <div class ="col-md-8">
                            <select id="district_seq" name="district_seq" id="district_seq_label"> 
                               
                                <?php  
                               echo '<option value="' . $member->district_seq . '">' . $member->district_name . '</option>';
                               
                      
                                ?>
                                 <option value=""></option>
                            </select>
                                 
                        </div>
                    </div> <?php */?>
                     <div class ="form-group">
                        <label for="f_district">Kode Pos</label>
                        <div class ="col-md-8">
                              <input class="form-control" name="zip_code" type="text" value ="<?php  echo (isset($member->zip_code) ? $member->zip_code : "") ?>">  
                        </div>
                    </div>
					<div class ="form-group">
                        <label>District Seq</label>
                        <div class ="col-md-8">
                              <input class="form-control" name="district_seq" type="text" value ="<?php  echo (isset($member->district_seq) ? $member->district_seq : "") ?>">  
                        </div>
                    </div>
               <!--   </form> -->

                    <input type="submit" value="Simpan Data" >
                <?php echo form_close(); ?>
      
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">
 
$('#f_province').change(function(){
	alert('aaa');
    var province_seq = $('#f_province').val();
    if (province_seq != ""){
         var post_url = "http://localhost/api.toko1001.id/v1/member/cities/" + province_seq;
        $.ajax({
            type: "POST",
             url: post_url,
             success: function(cities) //we're calling the response json array 'cities'
              {
                $('#f_city').empty();
                $('#f_city, #f_city_label').show();
                   $.each(cities,function(seq,name) 
                   {
                    var opt = $('<option />'); // here we're creating a new select option for each group
                      opt.val(seq);
                      opt.text(name);
                      $('#f_city').append(opt); 
                });
               } //end success
         }); //end AJAX
    } else {
        $('#f_city').empty();
        $('#f_city, #f_city_label').hide();
    }//end if
}); //end change 


$('#f_city').change(function(){
    var city_seq = $('#f_city').val();
    if (city_seq != ""){
         var post_url = "http://localhost/api.toko1001.id/v1/member/districts/" + city_seq;
        $.ajax({
            type: "POST",
             url: post_url,
             success: function(districts) //we're calling the response json array 'cities'
              {
                $('#district_seq').empty();
                $('#district_seq, #f_district_label').show();
                   $.each(districts,function(seq,name) 
                   {
                    var opt = $('<option />'); // here we're creating a new select option for each group
                      opt.val(seq);
                      opt.text(name);
                      $('#district_seq').append(opt); 
                });
               } //end success
         }); //end AJAX
    } else {
        $('#district_seq').empty();
        $('#district_seq, #district_seq_label').hide();
    }//end if
}); //end change 

// function add_address()
// {

//     var url;
//    // url = "<?php echo site_url('v1/member/save_add_address')?>";
//     url = "http://localhost/api.toko1001.id/v1/member/save_add_address";
//     // ajax adding data to database
//     $.ajax({
//         url : url,
//         type: "POST",
//         data: $('#form').serialize(),
//         dataType: "JSON",

//     });
// }
</script>
</body>
</html>
