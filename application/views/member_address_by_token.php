
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
                    <input name="member_seq"  value="1"/> <br/>
                    <input name="token"  value="6eac203b659abd8dc60a5e0cbcd232c219334330" /> <br/> 
                   
                    <input name= 'action' value = '4' type=""> 
                 
                    
               <!--   </form> -->

                    <input type="submit" value="Get List Data" >
                <?php echo form_close(); ?>
      
                </div>
            </div>
        </div>
    </div>

</body>
</html>
