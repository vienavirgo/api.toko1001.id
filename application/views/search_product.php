
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Search Window</title>
 
</head>
<body>
    <div id="container">
    <h1>Search</h1>
        <div id="body">
        <form action="<?= base_url().'v1/product/kategori'; ?>" method="get">       
        <div class="form-body">
            <div class="form-group">
                <label class="control-label col-md-3">Cari</label>
                <div class="col-md-9">
                    <input name="e" class="form-control" type="text"  >
                </div>
            </div>           
            <input type="submit" class="btn" value="Cari Data">            
            </div>
        </form>        
    </div>
</body>
</html>
