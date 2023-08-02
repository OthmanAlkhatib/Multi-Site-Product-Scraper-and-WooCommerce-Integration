<?php

require_once __DIR__."./../../../../wp-config.php";
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

header('Access-Control-Allow-Origin: *');
header("Content-Type:application/json; charset=utf-8");


class requestData{
    public $data;
    private $con;
    
    public function  __construct($data)
    {
        $this->data=$data;
            
        $this->post_id = null;
        
        $this->con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        
        // Check Connection
        if (!$this->con) {
            die("Connecting to Database Has Been Failed" . mysqli_connect_error());
        }
        
        mysqli_set_charset($this->con, "utf8");
    }
    
    private function upload_all_images_to_product($product_id, $image_id_array) {
    //take the first image in the array and set that as the featured image
    // set_post_thumbnail($product_id, $image_id_array[0]);

    //if there is more than 1 image - add the rest to product gallery
    if(sizeof($image_id_array) > 1) {
        // array_shift($image_id_array); //removes first item of the array (because it's been set as the featured image already)
        update_post_meta($product_id, '_product_image_gallery', implode(',',$image_id_array)); //set the images id's left over after the array shift as the gallery images
    }
}
    
    public function insert()
    {
        // Prepare Description and Atrributes for Product Page
        $str_discription_attributes = "";
        foreach($this->data->discription as $disc){
            $str_discription_attributes .= $disc . "\n\n\n";
        }
        foreach($this->data->attributes as $attr){
            $str_discription_attributes .= $attr . "\n\n\n";
        }
        
        // Bulid Post
        $post = array(
                // 'post_author' => 'othman',
                'post_content' => $str_discription_attributes,
                'post_status' => "publish",
                'post_title' => $this->data->title,
                'post_parent' => "",
                'post_type' => "product",
        );
            
        //Create Post
        $post_id = wp_insert_post( $post );
        
        // Escaping Strings
        $escaped_title = $this->con->real_escape_string(str_replace("'", "\'", $this->data->title));
        
        if($this->data->photos) {
            $escaped_photos = array();
            foreach($this->data->photos as $photo){
                $escaped_photos[] = $this->con->real_escape_string(str_replace("'","\'",$photo));
            }
            $escaped_photos = json_encode($escaped_photos, JSON_HEX_APOS);
        }
        else
            $escaped_photos = "null";
            
        if($this->data->discription){
            $escaped_discription = array();
            foreach($this->data->discription as $disc){
                $escaped_discription[] = $this->con->real_escape_string(str_replace("'","\'",$disc));
            }
            $escaped_discription = json_encode($escaped_discription, JSON_HEX_APOS);
        }
        else
            $escaped_discription = "null";
        
        if($this->data->attributes){
            $escaped_attributes = array();
            foreach($this->data->attributes as $attr){
                $escaped_attributes[] = $this->con->real_escape_string(str_replace("'","",$attr));
            }
            $escaped_attributes = json_encode($escaped_attributes, JSON_HEX_APOS);
        }
        else
            $escaped_attributes = "null";
        
        // Create MySQL Query
        $sql_insert = "INSERT INTO `products`(`title`, `main_image`, `discription`, `attributes`, `photos`, `actual_price`, `new_price`, `product_url`, `post_id`) VALUES (";
        $sql_insert.="'{$escaped_title}',";
        $sql_insert.="'{$this->data->main_image}',";
        $sql_insert.="'{$escaped_discription}',";
        $sql_insert.="'{$escaped_attributes}',";
        $sql_insert.="'{$escaped_photos}',";
        $sql_insert.="'{$this->data->price}',";
        $sql_insert.="'{$this->data->new_price}',";
        $sql_insert.="'{$this->data->product_url}',";
        $sql_insert.="'{$post_id}');";
        
        // $this->con->query($sql_insert);
        // die ($this -> con -> error);
        
        if($this->con->query($sql_insert) === true){
            // Fill Post Details
            wp_set_object_terms( $post_id, $this->data->category, 'product_cat' );
            wp_set_object_terms( $post_id, 'simple', 'product_type');
            
            update_post_meta( $post_id, '_price', $this->data->new_price );
            
            $image = media_sideload_image( $this->data->main_image, $post_id, null, 'id' );
            set_post_thumbnail( $post_id, $image );
            
            $images_ids = array();
            foreach ($this->data->photos as $photo){
                $images_ids[] = media_sideload_image( $photo, $post_id, null, 'id' );
            }
            $this -> upload_all_images_to_product($post_id, $images_ids);
            
            $this->post_id = $post_id;
            return true;
        }
        else {
            wp_delete_post($post_id);
            return false;
        }
    }
    
    public function validate()
    {
        return $this->validate_required($this->data->title) && $this->validate_required($this->data->main_image) && $this->validate_required($this->data->price) && $this->validate_required($this->data->new_price) && $this->validate_required($this->data->product_url);
    }
    
    private function validate_required($entity)
    {
        return !is_null($entity) && $entity!='null' && $entity!='NULL' && $entity!='';
    }
}


try{
    $json = file_get_contents('php://input');
    $data = json_decode($json);

    //validation
    if($data) {
        $request_data = new requestData($data);
       
        if(!$request_data->validate($data)) {
            $response['status']=422;
            $response['error']='validation error';
            http_response_code(422);
            echo json_encode($response);
            exit;
        }
        
        if($request_data->insert()) {
            $response['status']=200;
            $response['message']='Product added successfully';
            $response['post_id']=$request_data->post_id;
            http_response_code(200);
            echo json_encode($response);
            exit;
        }
        else {
            $response['status']=422;
            $response['message']='Error while inserting product';
            http_response_code(422);
            echo json_encode($response);
            exit;
        }
    }
}
catch(Exception $e) {
    $response['status']=500;
    $response['error']= 'Server error';
    http_response_code(500);
    echo json_encode($response);
    exit; 
}


