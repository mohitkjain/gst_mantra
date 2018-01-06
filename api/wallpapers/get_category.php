<?php

class Category
{
    public $category_id;
    public $category_name;
    public $category_image = array();
}
$app->get('/wallpapers/category/{app_id}/{startAt}', function ($request, $response) 
{
    require_once '../api/wallpapers/settings/dbconnect.php';
    require_once '../api/wallpapers/settings/config.php';

    $app_id = $request->getAttribute('app_id');
    $startAt = $request->getAttribute('startAt');
    $startAt = intval($startAt);
    try
    {
        $con = connect_db();
        $config = new Config();
        $pagination = new Data_Details();
        $maxResult = 10;
        //Prepare a Query Statement
        $sql = "SELECT `id`, `category`, (SELECT COUNT(*) FROM category WHERE `app_id` = :app_id) AS 'total_rows' 
                FROM category
                WHERE `app_id` = :app_id
                LIMIT :startAt, :maxResult";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':app_id', $app_id, PDO::PARAM_INT);
        $stmt->bindParam(':startAt', $startAt, PDO::PARAM_INT);
        $stmt->bindParam(':maxResult', $maxResult, PDO::PARAM_INT);
        if ($stmt->execute()) 
        {
            $category_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $myArray = array();
            $imageArray = array();
            foreach($category_data as $data)
            {
                $obj = new Category();
                $obj->category_id = $data['id'];
                $obj->category_name = $data['category'];
                //Get 3 Images
                $sql_image = "SELECT `image_name` FROM `image_gallery` WHERE `category_id` = :category_id
                              ORDER BY `votes` DESC
                              LIMIT 4";
                $stmt_image = $con->prepare($sql_image);
                $stmt_image->bindParam(':category_id', $data['id']);

                if($stmt_image->execute())
                {
                    $category_images = $stmt_image->fetchAll(PDO::FETCH_ASSOC);
                    foreach($category_images as $image)
                    {
                        $imageArray[] = $config->thumb_address.'/'.$image['image_name'];
                    }
                }
                $obj->category_image = $imageArray;

                $total = $data['total_rows'];
                array_push($myArray, $obj);
                unset($imageArray);
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

$app->get('/wallpapers/category/{app_id}', function ($request, $response) 
{
    require_once '../api/wallpapers/settings/dbconnect.php';
    require_once '../api/wallpapers/settings/config.php';

    $app_id = $request->getAttribute('app_id');
    $startAt = 0;
    try
    {
        $con = connect_db();
        $config = new Config();
        $pagination = new Data_Details();
        $maxResult = 10;
        //Prepare a Query Statement
        $sql = "SELECT `id`, `category`, (SELECT COUNT(*) FROM category WHERE `app_id` = :app_id) AS 'total_rows' 
                FROM category
                WHERE `app_id` = :app_id
                LIMIT :startAt, :maxResult";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':app_id', $app_id, PDO::PARAM_INT);
        $stmt->bindParam(':startAt', $startAt, PDO::PARAM_INT);
        $stmt->bindParam(':maxResult', $maxResult, PDO::PARAM_INT);
        if ($stmt->execute()) 
        {
            $category_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $myArray = array();
            $imageArray = array();
            foreach($category_data as $data)
            {
                $obj = new Category();
                $obj->category_id = $data['id'];
                $obj->category_name = $data['category'];
                //Get 3 Images
                $sql_image = "SELECT `image_name` FROM `image_gallery` WHERE `category_id` = :category_id
                              ORDER BY `votes` DESC
                              LIMIT 4";
                $stmt_image = $con->prepare($sql_image);
                $stmt_image->bindParam(':category_id', $data['id']);

                if($stmt_image->execute())
                {
                    $category_images = $stmt_image->fetchAll(PDO::FETCH_ASSOC);
                    foreach($category_images as $image)
                    {
                        $imageArray[] = $config->thumb_address.'/'.$image['image_name'];
                    }
                }
                $obj->category_image = $imageArray;

                $total = $data['total_rows'];
                array_push($myArray, $obj);
                unset($imageArray);
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