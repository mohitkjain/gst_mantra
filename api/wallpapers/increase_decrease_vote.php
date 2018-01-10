<?php

class Vote_Image
{
    public $result;
    public $image_id;
    public $votes;
}
$app->post('/wallpapers/category/images/updates/votes', function ($request, $response) 
{
    require_once '../api/wallpapers/settings/dbconnect.php';
    require_once '../api/wallpapers/settings/config.php';

    $parsedBody = $request->getParsedBody();
    $image_id = $parsedBody['image_id'];
    $vote = $parsedBody['vote'];

    try
    {
        $con = connect_db();
        $config = new Config();
        $vote_image = new Vote_Image();
        $sql = '';
        //Prepare a Query Statement
        if($vote == 1)
        {
            $sql = "UPDATE `image_gallery` 
                SET `votes` = `votes` + 1
                WHERE `id` = :image_id";
        }
        else if($vote == -1)
        {
            $sql = "UPDATE `image_gallery` 
                SET `votes` = `votes` - 1
                WHERE `id` = :image_id";
        }
        
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
        if ($stmt->execute()) 
        {
            $count = $stmt->rowCount();
            $result;
            if($count > 0)
            {
                $result = "success";
            }
            else
            {
                $result = "failure";
            }
            $sql = "SELECT `votes` FROM `image_gallery` WHERE `id` = :image_id";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);

            if($stmt->execute())
            {
                $votes_data = $stmt->fetch(PDO::FETCH_ASSOC);
                $vote_image->image_id = $image_id;
                $vote_image->votes = $votes_data['votes'];
                $vote_image->result = $result; 
            }
            else
            {
                throw new PDOException('Can not updated the vote.');
            }

            if($vote_image) 
            {
                return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($vote_image));
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