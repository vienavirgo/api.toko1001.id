
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Single Window</title>

    </head>
    <body>
        <div id="container">
            <h1>Member</h1>
            <div id="body">
                //<?php //echo form_open('v1/member/info'); ?>
<!--                <form method="post" action="http://localhost/api.toko1001.id/v1/member/address">         -->
                    <form method="post" action="http://localhost/api.toko1001.id/v1/member/">
					<div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3">Name</label>
                            <div class="col-md-9">
                                <input name="name" placeholder="Name" class="form-control" type="text"  value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Email</label>
                            <div class="col-md-9">
                                <input name="email" placeholder="Name" class="form-control" type="text"  value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Password</label>
                            <div class="col-md-9">
                                <input name="password" placeholder="Name" class="form-control" type="password"  value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Phone</label>
                            <div class="col-md-9">
                                <input name="mobile_phone" placeholder="Phone" class="form-control" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">Date of Birth</label>
                            <div class="col-md-9">
                                <input name="birthday" placeholder="yyyy-mm-dd" class="form-control datepicker" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Gender</label>
                        <div class="col-md-9">
                            <select name="gender" class="input-read-only">
                                <option value="male" >Male</option>
                                <option value="female">Female</option>                   
                            </select>
                        </div>
                    </div>
                    <input type="submit" class="btn-kirim-login" value="Simpan Data">
                    <?php echo form_close(); ?>
                    </div>
                    </div>
                    </body>
                    </html>
