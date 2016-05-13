<!--LOGIN : <br>
<form method="post" action="<?=base_url();?>v1/member/login">
    <input type="email" name="email" placeholder = "email">
    <input type="password" name="password" placeholder = "md5(password)">
    <input type="submit" name="test">
</form>-->

<!--CHANGE PASSWORD : <br>
<form method="post" action="http://localhost/api.toko1001.id/v1/member/change_password">    
	<input type="password" name="old_password" placeholder = "md5 old password">
    <input type="password" name="new_password" placeholder = "md5 new password">
    <input type="text" name="token" placeholder = "token ">
    <input type="text" name="member_seq" placeholder = "member seq ">
    <input type="submit" name="test">
</form>-->
<!--ADD WISHLIST : <br>
<form method="post" action="http://localhost/api.toko1001.id/v1/member/wishlist">    
    <input type="text" name="product_variant_seq" placeholder="product_variant_seq">    
    <input type="text" name="token" placeholder = "token">
    <input type="hidden" name="action" value="1">
    <input type="text" name="member_seq" placeholder = "member seq ">
    <input type="submit" name="test">
</form>-->
<!--DELETE WISHLIST : <br>
<form method="post" action="http://localhost/api.toko1001.id/v1/member/wishlist">    
    <input type="text" name="product_variant_seq" placeholder="product_variant_seq">    
    <input type="text" name="token" placeholder = "token">
    <input type="" name="action" value="3" placeholder="action">
    <input type="text" name="member_seq" placeholder = "member seq ">
    <input type="submit" name="test">
</form>-->

<!--<form method="post" action="http://localhost/api.toko1001.id/v1/member/account">   -->
Account Lists: <br>
<form method="post" action="http://192.168.50.117/v1/member/order"> 
    <input type="hidden" name="action" value="4">
    <input type="text" name="member_seq" placeholder = "member seq ">
	<input type="text" name="fieldname" placeholder = "fieldname">
	<input type="text" name="token" placeholder = "token">
	<input type="text" name="mulai" placeholder = "mulai" value="10">
	<input type="text" name="page" placeholder = "page">
	<select name="order">
		<option value="ASC">ASC</option>
		<option value="DESC">DESC</option>
	</select>
    <input type="submit" name="test">
</form>
<!--
<a href="../controllers/V1/Member.php"></a>-->

<!--Expedition : <br>
<form method="post" action="expedition">    
    <input type="text" name="product_variant_seq" placeholder="product_variant_seq">  
	<input type="text" name="qty" placeholder = "Quantity">
    <input type="text" name="token" placeholder = "token">
    <input type="" name="action" value="4">
    <input type="text" name="member_seq" placeholder = "member seq ">
	<input type="text" name="district_seq" placeholder = "district seq ">
    <input type="submit" name="test">
</form>-->
<!--
Member Address : <br>
<form method="post" action="member/address">    
    <input type="text" name="member_seq" placeholder="member_seq">  
    <input type="text" name="alias" placeholder="alias"> 
	 <input type="text" name="address_seq" placeholder="address_seq">  
    <input type="text" name="token" placeholder = "token">
    <input type="" name="action" value="2">
    <input type="submit" name="test">
</form>-->

