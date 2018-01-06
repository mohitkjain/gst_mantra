<?php

class Images
{
    public $image_id;
    public $image_name;
    public $large_image;
    public $thumb_image;
    public $votes;
}
$app->get('/wallpapers/{app_id}/category/{category_id}/{startAt}', function ($request, $response) 
{
    require_once '../api/wallpapers/settings/dbconnect.php';
    require_once '../api/wallpapers/settings/config.php';

    $app_id = $request->getAttribute('app_id');
    $startAt = $request->getAttribute('startAt');
    $category_id = $request->getAttribute('category_id');
    $startAt = intval($startAt);
    try
    {
        $con = connect_db();
        $config = new Config();
        $pagination = new Data_Details();
        $maxResult = 10;
        //Prepare a Query Statement
        $sql = "SELECT `id`, `image_title`, `image_name`, `votes`, (SELECT COUNT(*) FROM `image_gallery` WHERE `category_id` = :category_id) AS 'total'
                FROM `image_gallery` 
                WHERE `category_id` = :category_id
                ORDER BY `votes` DESC
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
                $obj->large_image = $config->large_address.'/'.$data['image_name']; 
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

$app->get('/wallpapers/{app_id}/category/{category_id}', function ($request, $response) 
{
    require_once '../api/wallpapers/settings/dbconnect.php';
    require_once '../api/wallpapers/settings/config.php';

    $app_id = $request->getAttribute('app_id');
    $startAt = 0;
    $category_id = $request->getAttribute('category_id');
    try
    {
        $con = connect_db();
        $config = new Config();
        $pagination = new Data_Details();
        $maxResult = 10;
        //Prepare a Query Statement
        $sql = "SELECT `id`, `image_title`, `image_name`, `votes`, (SELECT COUNT(*) FROM `image_gallery` WHERE `category_id` = :category_id) AS 'total'
                FROM `image_gallery` 
                WHERE `category_id` = :category_id
                ORDER BY `votes` DESC
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
                $obj->large_image = $config->large_address.'/'.$data['image_name']; 
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