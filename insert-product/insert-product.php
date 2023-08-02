<?php
/**
 * Plugin Name:insert-product
 * Description: to add new products from other sites
 * Version: 1.0
 * Author: TAD Center
 */
 
include("api/connect.php");
header('Access-Control-Allow-Origin: *');

// Creating Required Tables.
$sql_create_products_table = "CREATE TABLE IF NOT EXISTS products (
  id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(300) NOT NULL,
  main_image TEXT NOT NULL,
  description TEXT,
  attributes TEXT,
  photos TEXT,
  actual_price VARCHAR(20) NOT NULL,
  new_price VARCHAR(20) NOT NULL,
  product_url TEXT NOT NULL,
  post_id INT(11)
)";
$sql_create_notify_table = "CREATE TABLE IF NOT EXISTS notify (id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, is_changed VARCHAR(20) NOT NULL)";
$con -> query($sql_create_products_table);
$con -> query($sql_create_notify_table);

add_action('admin_menu', 'product_page');
function product_page(){
    add_menu_page('Insert New Product', 'Insert New Product', 'manage_options',  'insert_product','insert_product_fun','dashicons-download',2 );
    add_submenu_page('insert_product', 'Check Products', 'Check Products', 'manage_options', 'check_products', 'check_products_fun');
}


function insert_product_fun () {
?>
<html lang="en">
    
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
      <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<!-- data table -->
    	<script src="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css"></script>
    	<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
</head>
<style>
    .info_show {
        color: dodgerblue;
      
    }
    .active{
        background: #1b374d !important;
        color: white !important;
    }
    .form-control {
        border: 1px solid #8c8f94;
        color: #1b374d;
        background: white;
        box-shadow: -1px 9px 12px -8px rgb(0 0 0 / 75%) !important;
    }
     
    .dataTables_length select {
        color: #1b374d;
        background: white;
        border-radius: 30px;
        margin: 10px;
        padding: 6px 17px;
        box-shadow: 0px 9px 12px -8px rgb(0 0 0 / 75%);
    }
    .btn1 {
        border: 1px solid #8c8f94;
        color: #ffffff;
        background: #09669e;
        margin: 30px;
        padding: 6px 17px;
        box-shadow: 0px 9px 12px -8px rgb(0 0 0 / 62%);
        height: 41px;
    } .dropdown{
                position: relative;
                display: inline-block;
            }
    .dropdown:checked ~ .dropdown-content {display: block;}
    .dropdown {
      position: relative;
      display: inline-block;
    }
    
    .dropdown-content {
        left:-40px;
      display: none;
      position: absolute;
      background-color: white;
      min-width: 95px;
      box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
      z-index: 1;
    }
    
    .dropdown-content i {
      color: black;
      padding: 5px 0px;
      text-decoration: none;
      display: block;
      
    }
    
    .dropdown-content i:hover {background-color: #ddd;}
    
    .dropdown:hover .dropdown-content {display: block;}
    input[type="search"] {
        border: 1px solid #8c8f94;
        color: #1b374d;
        background: white;
        border-radius: 30px;
        margin: 10px;
        padding: 3px 17px;
        box-shadow: 0px 9px 12px -8px rgb(0 0 0 / 75%);
    }
    .cell {
      border-collapse: collapse;
      border: 1px solid #dee2e6;
      
      padding: 5px 10px;
    }
    
    .table {
      border-collapse: collapse;
      border-style: hidden !important;
      
    }
    
    .table-head > .row {
      border-collapse: collapse;
      border: 2px solid #dee2e6;
    }
    .table_div{
        border: 1px solid #9b9ea2;
        color: #1b374d;
        background: white;
        border-radius: 10px;
        padding: 50px 17px;
        margin-bottom: 20px;
        box-shadow: 0px 9px 12px -8px rgb(0 0 0 / 75%);
    
    }
    div#singledate_table_paginate {
        display: flex;
        width: 42%;
        margin-right: auto;
        justify-content: space-evenly;
    }
    div#singledate_table_info {
        text-align: right;
        margin-right: 20px;
    }
    #loading-circle{
        width: 175px;
        height: 175px;
        border-radius: 50%;
        border: 15px solid #09669e;
        border-right: 15px solid transparent;
        margin: auto;
        margin-top: 120px;
        display: none;
        animation-name: rotating;
        animation-duration: 0.8s;
        animation-iteration-count: infinite;
        animation-timing-function: linear;
      }
    @keyframes rotating{
      to{
        transform: rotate(360deg);
      }
    }
</style>
<body>
    <div id="content">
        <div class="col-sm-12" style="text-align: center;padding: 45px;    padding-bottom: 0px;">
            <h1 style="color: #424547;font-size: 43px;">Insert Product</h1>
            <hr>
        </div>
        <div class="row" style=" margin-left: 203px;">
            <div class="col-sm-6">
                <div class="form-group" >
                    <label for="name"> Enter Product URL : </label>
                    <input type="text" class="form-control" id="the_url" style="height: 45px;" name="the_url">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <button class=" btn btn1" onclick="get_product_ByLink()">Get Product</button>
                </div>
            </div>
        </div>
    
        <div class="col-sm-12 " style="text-align: center;padding: 45px;     padding-top: 10px;">
           <table class="table  table-bordered table_div" id="singledate_table" style="text-align: center;">
                <thead>
                    <tr>
                        <th style="color: white;background: #09669e;">Product</th>
                        <th style="color: white;background: #09669e;">main photo</th>
                        <th style="color: white;background: #09669e;">description</th>
                        <th style="color: white;background: #09669e;">attributes</th>
                        <th style="color: white;background: #09669e;">photos</th>
                        <th style="color: white;background: #09669e;">Price</th>
                        <th style="color: white;background: #09669e;">Availability</th>
                        <th style="color: white;background: #09669e;">Category</th>
                        <th style="color: white;background: #09669e;">Insert</th>
                    </tr>
                </thead>
                <tbody id="d2">
                    
                </tbody>
            </table>
        </div>
    </div>
    <div id="loading-circle"></div>
</body>
<script>
var global_data = {};
var categories = [];

// Get Categories
$.ajax({
    type: "GET",
    url: "YOUR_WORDPRESS_DOMAIN/wp-content/plugins/insert-product/api/get_categories.php",
    dataType: 'json',
    success: function(response) {
        categories = response;
    },
    error: function(error) {
        categories.push('null');
    }
})

function get_product_ByLink(){
    var api_url = "YOUR_DJANGO_DOMAIN/api/get-product?url=";
    // var api_url = "http://127.0.0.1:8000/api/get-product?url=";
    var product_url = document.getElementById('the_url').value;
    document.getElementById('content').style.display = "none";
    document.getElementById('loading-circle').style.display = "block";
    $.ajax({
            type: 'GET',
            url: api_url + product_url,
            dataType: 'json',
            success: function(response){
                document.getElementById('content').style.display = "block";
                document.getElementById('loading-circle').style.display = "none";
                var data = response;
                global_data = response;
                var tds = "";
                $.each(data, function(key) {
                    if (key == "main_image") {
                        if (data[key] != 'null'){
                            tds += `<td><img src='${data[key]}' style="width: 200px; height:200px"></td>`;
                        }
                        else {
                            tds += `<td>${data[key]}</td>`;
                        }
                    }
                    else if (key == "photos") {
                        if (data[key] != 'null'){
                            var imgs = '';
                            for(let img of data[key]) {
                                imgs += `<img src='${img}' style='width: 120px; height: 120px'>`;
                            }
                            tds += `<td style="display: flex;flex-direction: column;gap: 20px;">${imgs}</td>`;
                        }
                        else {
                            tds += `<td>${data[key]}</td>`;
                        }
                    }
                    else if (key == "price") {
                        tds += `<td>${data[key]} <br><br> New Price: <input type="text" class="form-control" id="new_price" style="height: 25px;" name="new_price"> </td>`;
                    }
                    else {
                        tds += `<td>${data[key]}</td>`;
                    }
                });
                var options = "<option value=''></option>";
                for(let category of categories) {
                    options += `<option value='${category}'>${category}</option>`;
                }
                var select = `<td><select id='category'>${options}</select></td>`;
                tds += select;
                tds += `<td><button onClick="insert_to_database()" style="color: white;background: #09669e; border-radius: 4px; padding: 1px 5px; outline:none; border:none">Insert</button></td>`
                $("#d2").html(tds);
            },
            error: function(error) {
                document.getElementById('content').style.display = "block";
                document.getElementById('loading-circle').style.display = "none";
                console.log(error)
                alert("Error");
            }
        })
}

function insert_to_database() {
    global_data.new_price = document.getElementById('new_price').value;
    global_data.product_url = document.getElementById('the_url').value;
    global_data.category = document.getElementById('category').value;
    var settings = {
        "url": "YOUR_WORDPRESS_DOMAIN/wp-content/plugins/insert-product/api/insert_product_to_db.php",
        "method": "POST",
        "timeout": 0,
        "headers": {
            "Content-Type": "application/json"
        },
        "data": JSON.stringify(global_data),
        success: function(response) {
            if(window.confirm("Done, Go to see product ?")){
                window.location = `YOUR_WORDPRESS_DOMAIN/${response.post_id}`;
            }
        },
        error: function(error) {
            alert("Error");
            console.log(error);
        }
    };
    $.ajax(settings)
}
</script>

</html>
<?php
}
?>



<?php
function check_products_fun() {
    // Get Products to proccess
    $sql_select = "SELECT * FROM `products`";
    global $con;
    $result = $con->query($sql_select);
    $sql_data = array();
    
    While($row = $result->fetch_assoc()) {
        $sql_data[] = $row;
    }
?>
<html>
    <head>
        <title>Bootstrap Example</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    	<!-- data table -->
        <script src="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css"></script>
        <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    </head>
    <style>
        .info_show {
            color: dodgerblue;
          
        }
        .active{
            background: #1b374d !important;
            color: white !important;
        }
        .form-control {
            border: 1px solid #8c8f94;
            color: #1b374d;
            background: white;
            box-shadow: -1px 9px 12px -8px rgb(0 0 0 / 75%) !important;
        }
         
        .dataTables_length select {
            color: #1b374d;
            background: white;
            border-radius: 30px;
            margin: 10px;
            padding: 6px 17px;
            box-shadow: 0px 9px 12px -8px rgb(0 0 0 / 75%);
        }
        .btn1 {
            border: 1px solid #8c8f94;
            color: #ffffff;
            background: #09669e;
            margin: 30px;
            padding: 10px 60px;
            box-shadow: 0px 9px 12px -8px rgb(0 0 0 / 62%);
            /*height: 41px;*/
        } .dropdown{
                    position: relative;
                    display: inline-block;
                }
        .dropdown:checked ~ .dropdown-content {display: block;}
        .dropdown {
          position: relative;
          display: inline-block;
        }
        
        .dropdown-content {
            left:-40px;
          display: none;
          position: absolute;
          background-color: white;
          min-width: 95px;
          box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
          z-index: 1;
        }
        
        .dropdown-content i {
          color: black;
          padding: 5px 0px;
          text-decoration: none;
          display: block;
          
        }
        
        .dropdown-content i:hover {background-color: #ddd;}
        
        .dropdown:hover .dropdown-content {display: block;}
        input[type="search"] {
            border: 1px solid #8c8f94;
            color: #1b374d;
            background: white;
            border-radius: 30px;
            margin: 10px;
            padding: 3px 17px;
            box-shadow: 0px 9px 12px -8px rgb(0 0 0 / 75%);
        }
        .cell {
          border-collapse: collapse;
          border: 1px solid #dee2e6;
          
          padding: 5px 10px;
        }
        
        .table {
          border-collapse: collapse;
          border-style: hidden !important;
          
        }
        
        .table-head > .row {
          border-collapse: collapse;
          border: 2px solid #dee2e6;
        }
        .table_div{
            border: 1px solid #9b9ea2;
            color: #1b374d;
            background: white;
            border-radius: 10px;
            padding: 50px 17px;
            margin-bottom: 20px;
            box-shadow: 0px 9px 12px -8px rgb(0 0 0 / 75%);
        
        }
        td {
            text-align: center !important;
            vertical-align: middle !important;
        }
        div#singledate_table_paginate {
            display: flex;
            width: 42%;
            margin-right: auto;
            justify-content: space-evenly;
        }
        div#singledate_table_info {
            text-align: right;
            margin-right: 20px;
        }
        #loading-circle{
            width: 175px;
            height: 175px;
            border-radius: 50%;
            border: 15px solid #09669e;
            border-right: 15px solid transparent;
            margin: auto;
            margin-top: 120px;
            display: none;
            animation-name: rotating;
            animation-duration: 0.8s;
            animation-iteration-count: infinite;
            animation-timing-function: linear;
        }
        @keyframes rotating{
          to{
            transform: rotate(360deg);
          }
        }
        #update-button{
            display: none;
            padding: 10px 30px;
            margin: 0;
            margin-left: 30px;
        }
    </style>
    <body>
        <div class="row justify-content-center" id="content">
            <div class="col-sm-10" style="text-align: center;padding: 45px; padding-bottom: 0px;">
                <h1 style="color: #424547;font-size: 43px;">Check Products</h1>
                <hr>
            </div>
            <div class="col-sm-10">
                <div class="form-group text-center">
                    <button class="btn btn1 ps-4 pe-4 pt-2 pb-2" onclick="check_products()">Check</button>
                </div>
            </div>
            <div class="col-sm-10">
                <div class="text-center d-flex justify-content-center align-items-center mb-4">
                    <h4 id="progress"></h4>
                    <button id="update-button" class="btn btn1" onclick="update_products()">Update</button>
                </div>
            </div>
        
            <div class="col-sm-10 table_container" style="text-align: center;padding: 45px; padding-top: 10px;">
               <table class="table  table-bordered table_div" id="singledate_table">
                    <thead>
                        <tr>
                            <th style="color: white;background: #09669e;">Product</th>
                            <th style="color: white;background: #09669e;">Main Photo</th>
                            <!--<th style="color: white;background: #09669e;">Category</th>-->
                            <th style="color: white;background: #09669e;">Old Price</th>
                            <th style="color: white;background: #09669e;">New Price</th>
                            <th style="color: white;background: #09669e;">Edit</th>
                        </tr>
                    </thead>
                    <tbody id="d2">
                        
                    </tbody>
                </table>
                <h2 id='no-changes'></h2>
            </div>
        </div>
        <div id="loading-circle"></div>
    </body>
    <script type="text/javascript">
        var changed_products = [];
        
        function check_products(){
            document.getElementById('content').style.display = "none";
            document.getElementById('loading-circle').style.display = "block";
            document.getElementById("d2").innerHTML = "";
            document.getElementById("no-changes").innerHTML = "";
            
            var sql_data = <?php echo json_encode($sql_data); ?>;
            var api_url = "YOUR_DJAMGO_DOMAIN/api/get-product?url=";
            
            var not_changed = 0;
            var cnt = 0;
            var products_len = sql_data.length;
            for(let sql_product_data of sql_data) {
                var product_url = sql_product_data['product_url'];
                $.ajax({
                    type: 'GET',
                    url: api_url + product_url,
                    dataType: 'json',
                    success: function(response){
                        cnt += 1;
                        if (cnt == products_len){
                            document.getElementById('update-button').style.display = "block";
                            document.getElementById('content').style.display = "block";
                            document.getElementById('loading-circle').style.display = "none";
                        }
                        document.getElementById("progress").innerHTML = `Product ${cnt} of ${products_len}`;
                        
                        var api_product_data = response;
                        if (sql_product_data['actual_price'] == api_product_data['price']) {
                            not_changed += 1;
                            if (not_changed == products_len) {
                                text = document.createTextNode("No Changes.");
                                h2 = document.getElementById("no-changes");
                                h2.appendChild(text);
                            }
                        }
                        else {
                            document.getElementById('content').style.display = "block";
                            document.getElementById('loading-circle').style.display = "none";
                            changed_products.push({id: sql_product_data['id'], new_price: api_product_data['price']});
                            var tds = "";
                            tds += `<td><a href=${sql_product_data['product_url']}>${sql_product_data['title']}</a></td>`;
                            tds += `<td><img src='${api_product_data['main_image']}' style="width: 120px; height:120px"></td>`;
                            tds += `<td>${sql_product_data['actual_price']}</td>`;
                            tds += `<td style='color:red'>${api_product_data['price']}</td>`;
                            tds += `<td><a href=YOUR_WORDPRESS_DOMAIN/wp-admin/post.php?post=${sql_product_data['post_id']}&action=edit>Edit</a></td>`;
                            
                            var tr = `<tr>${tds}</tr>`;
                            $(`#d2`).append(tr);
                        }
                    },
                    error: function(error) {
                        document.getElementById('content').style.display = "block";
                        document.getElementById('loading-circle').style.display = "none";
                        // changed_products.push({id: sql_product_data['id'], new_price: api_product_data['price']});
                        var tds = "";
                        tds += `<td><a href=${sql_product_data['product_url']}>${sql_product_data['title']}</a></td>`;
                        tds += `<td><img src='${sql_product_data['main_image']}' style="width: 120px; height:120px"></td>`;
                        tds += `<td>${sql_product_data['actual_price']}</td>`;
                        tds += `<td style='color:red'>Error: NULL</td>`;
                        tds += `<td><a href=YOUR_WORDPRESS_DOMAIN/wp-admin/post.php?post=${sql_product_data['post_id']}&action=edit>Edit</a></td>`;
                        
                        var tr = `<tr>${tds}</tr>`;
                        $(`#d2`).append(tr);
                    }
                });
            }
        }
        
        function update_products() {
            $.ajax({
                type: "POST",
                dataType: "json",
                data: JSON.stringify(changed_products),
                url: "YOUR_WORDPRESS_DOMAIN/wp-content/plugins/insert-product/api/update_products.php",
                success: function(response) {
                    alert("Products Updated Successfully");
                    // console.log(response)
                    window.location.reload();
                },
                error: function(error){
                    alert("Error");
                    console.log(error);
                }
            })
        }
    </script>
</html>
<?php
}
?>

<?php
function on_delete_event($post_id){
    global $con;
    $sql_delete = "DELETE FROM products WHERE post_id={$post_id}";
    $result = $con->query($sql_delete);
}

function wpdocs_trash_posts( $post_id = '' ) {
    // Verify if is trashing multiple posts
    if ( isset( $_GET['post'] ) && is_array( $_GET['post'] ) ) {
        foreach ( $_GET['post'] as $post_id ) {
            on_delete_event( $post_id );
        }
    } else {
        on_delete_event( $post_id );
    }
}

add_action( 'wp_trash_post', 'wpdocs_trash_posts' );
?>



<?php
// Check for Notification
$sql_select = "SELECT * FROM `notify`";
$result = $con->query($sql_select);
$sql_data = array();

While($row = $result->fetch_assoc()) {
    $sql_data[] = $row;
}
$IS_CHANGED = $sql_data[0]["is_changed"];
if($IS_CHANGED == "true")
    add_action( 'admin_notices', 'sample_admin_notice__success' );
    
function sample_admin_notice__success() {
?>
    <div class="notice notice-error is-dismissible">
        <p><?php _e( '==== Some Products Prices Have Been Changed ====', 'sample-text-domain' ); ?></p>
    </div>
<?php
}
?>