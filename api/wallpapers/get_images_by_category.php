<?php

class Images
{
    public $image_id;
    public $image_name;
    public $category_name;
    public $large_image;
    public $mid_image;
    public $thumb_image;
    public $votes;
}
$app->get('/wallpapers/category/{category_id:\d+}[/{startAt:\d+}]', function ($request, $response) 
{
    require_once '../api/wallpapers/settings/dbconnect.php';
    require_once '../api/wallpapers/settings/config.php';

    $startAt = $request->getAttribute('startAt');
    $category_id = $request->getAttribute('category_id');
    if(!empty($startAt))
        $startAt = intval($startAt);
    else
        $startAt = 0;
    try
    {
        $con = connect_db();
        $config = new Config();
        $pagination = new Data_Details();
        $maxResult = 10;
        //Prepare a Query Statement
        $sql = "SELECT img.`id`, `image_title`, `image_name`, `votes`, cat.`category`, (SELECT COUNT(*) FROM `image_gallery` WHERE `category_id` = :category_id) AS 'total'
                FROM `image_gallery` img
                INNER JOIN `category` cat ON img.`category_id` = cat.`id`
                WHERE `category_id` = :category_id
                ORDER BY `date_updated` DESC, `votes` DESC
                LIMIT :startAt, :maxResult";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':app_id', $app_id, PDO::PARAM_INT);
        $stmt->bindParam(':startAt', $startAt, PDO::PARAM_INT);
        $stmt->bindParam(':maxResult', $maxResult, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        if ($stmt->execute()) 
        {
            $images_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $myArray = array();
            foreach($images_data as $data)
            {
                $obj = new Images();
                $obj->image_id = $data['id'];
                $obj->image_name = $data['image_title'];
                $obj->category_name = $data['category'];
                $obj->large_image = $config->large_address.'/'.$data['image_name'];
                $obj->mid_image =  $config->mid_address.'/'.$data['image_name'];
                $obj->thumb_image = $config->thumb_address.'/'.$data['image_name'];             
                $obj->votes = $data['votes'];

                $total = $data['total'];
                array_push($myArray, $obj);
            }

            $pagination->startAt = $startAt. "";
            $pagination->maxResults = $maxResult."";
            $pagination->total = $total;
            $pagination->data = $myArray;

            if($pagination) 
            {
                return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($pagination));
            } 
            else 
            { 
                throw new PDOException('No records found');
            }
        }           
    }
    catch(PDOException $e)
    {
        $errors = array();
        $errors[0]['result'] = "failure";
        $errors[0]['error_msg'] = $e->getMessage();
        return $response->withStatus(404)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($errors));
    }
});