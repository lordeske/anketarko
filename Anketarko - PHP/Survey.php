<?php

class Survey {
    
   public function listAllSurveys($dbcon){
        $sql = "SELECT * FROM surveys";
        $pdostm = $dbcon->prepare($sql);
        $pdostm->execute();
        $surveys = $pdostm->fetchAll(PDO::FETCH_OBJ);
        return $surveys;
    }
    
   
    public function AllSurveysTakenByUser($dbcon, $user_id) {
        $sql = "SELECT * FROM surveys 
                WHERE id IN (
                    SELECT DISTINCT(survey_id) 
                    FROM questions 
                    WHERE id IN (
                        SELECT DISTINCT(question_id) 
                        FROM answers 
                        WHERE user_id = :user_id
                    )
                ) AND visible = 1";
        $pst = $dbcon->prepare($sql);
        $pst->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $pst->execute();
        $allsurveys = $pst->fetchAll(PDO::FETCH_OBJ);
        return $allsurveys;
    }
    
   
    public function countSurveys($dbcon){
        $sql = "SELECT COUNT(id) as total FROM surveys";
        $pdostm = $dbcon->prepare($sql);
        $pdostm->execute();

        $surveys = $pdostm->fetchAll(\PDO::FETCH_OBJ);
        return $surveys;
    }

    
    public function countSurveys30($dbcon){
        $sql = "SELECT COUNT(id) as total30days FROM `surveys` WHERE DATE(created_date) >= DATE(NOW()) - INTERVAL 30 DAY";
        $pdostm = $dbcon->prepare($sql);
        $pdostm->execute();

        $surveys = $pdostm->fetchAll(\PDO::FETCH_OBJ);
        return $surveys;
    }
   
    public function addSurvey($name, $description, $userid, $categoryid, $dbcon)
    {
        $sql = "INSERT INTO surveys (title, description, user_id, category_id) 
              VALUES (:name, :description, :userid, :categoryid) ";
        $pst = $dbcon->prepare($sql);

        $pst->bindParam(':name', $name);
        $pst->bindParam(':description', $description);
        $pst->bindParam(':userid', $userid);
        $pst->bindParam(':categoryid', $categoryid);
        $count = $pst->execute();
        return $count;
    }
    
    public function showSurvey($dbcon, $id){
        //sql statement
        $sql = 'SELECT * FROM surveys WHERE id = :id';

        $pst = $dbcon->prepare($sql);
        $pst->bindParam(':id', $id);
        $pst->execute();

        $count = $pst->fetch(PDO::FETCH_OBJ);

        return $count;
    }

    public function updateSurvey($dbcon, $id, $title, $description, $category){
        $sql = 'UPDATE surveys SET title = :title, description = :description, category_id = :category_id WHERE id = :id';

        //prepare sql statement & bind params
        $pst = $dbcon->prepare($sql);

        $pst->bindParam(':title', $title);
        $pst->bindParam(':description', $description);
        $pst->bindParam(':category_id', $category);
        $pst->bindParam(':id', $id);

        //execute sql statement
        $count = $pst->execute();

        //return number of affected rows
        return $count;
    }
  
    public function deleteSurvey($dbcon, $id){
        $sql = "DELETE FROM surveys WHERE id = :id";

        $pst = $dbcon->prepare($sql);
        $pst->bindParam(':id', $id);
        $count = $pst->execute();
        return $count;
    }
    
    public function displaySurveys($dbcon, $user_id) {
        $sql = "SELECT surveys.id as survey_id, surveys.title, surveys.description, surveys.created_date, users.fname, users.lname 
                FROM surveys 
                JOIN categories ON surveys.category_id = categories.id 
                JOIN users ON surveys.user_id = users.id 
                WHERE users.id = :user_id AND surveys.visible = 1";
        $pst = $dbcon->prepare($sql);
        $pst->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $pst->execute();
        $indsurveys = $pst->fetchAll(PDO::FETCH_OBJ);
        return $indsurveys;
    }
    
 
    public function displayAllSurveys($dbcon){

        $sql = "SELECT * FROM `surveys` JOIN categories on surveys.category_id = categories.id JOIN users on surveys.user_id = users.id";
        $pst = $dbcon->prepare($sql);
        $pst->execute();
        $allsurveys= $pst->fetchAll(PDO::FETCH_OBJ);
        return $allsurveys;

    }
  
    public function avgResponse($dbcon, $survey_id){
        $sql = "SELECT CAST(AVG(answer) AS DECIMAL(10,2)) as average FROM answers inner join questions on answers.question_id=questions.id  where  survey_id= :survey_id";
        
        $pdostm = $dbcon->prepare($sql);
        $pdostm->bindParam(':survey_id', $survey_id);
        $pdostm->execute();

        $average = $pdostm->fetchAll(\PDO::FETCH_OBJ);
        return $average;
    }

    public function avgResponsePerUser($survey_id, $user_id, $db){
        $sql = "SELECT CAST(AVG(answer) AS DECIMAL(10,2)) as averageperuser from answers where user_id = :user_id AND question_id in (SELECT id from questions where survey_id = :survey_id)";
        
        $pdostm = $db->prepare($sql);
        $pdostm->bindParam(':user_id', $user_id);
        $pdostm->bindParam(':survey_id', $survey_id);
        $pdostm->execute();

        $averageperuser = $pdostm->fetch(\PDO::FETCH_ASSOC);
        return $averageperuser;
    }
   
    public function numberofuserspersurvey($survey_id, $db){
        $sql = "SELECT count(distinct(user_id)) as no_of_users from answers WHERE question_id in (SELECT id from questions where survey_id = :survey_id)";
        
        $pdostm = $db->prepare($sql);
        $pdostm->bindParam(':survey_id', $survey_id);
        $pdostm->execute();

        $noOfUsers = $pdostm->fetch(\PDO::FETCH_ASSOC);
        return $noOfUsers;
    }

    public function getQuestionsWithResponses($db, $survey_id) {
        $query = "
            SELECT q.question_text AS question, a.answer, COUNT(a.answer) as count
            FROM questions q
            JOIN answers a ON q.id = a.question_id
            WHERE q.survey_id = :survey_id
            GROUP BY q.question_text, a.answer
        ";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':survey_id', $survey_id, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $questionsWithResponses = [];
        foreach ($results as $result) {
            if (!isset($questionsWithResponses[$result['question']])) {
                $questionsWithResponses[$result['question']] = ['question' => $result['question'], 'answers' => []];
            }
            $questionsWithResponses[$result['question']]['answers'][$result['answer']] = $result['count'];
        }
    
        return array_values($questionsWithResponses);
    }


    public function searchSurveysByTitle($dbcon, $searchTerm) {
        $sql = "SELECT * FROM surveys WHERE title LIKE :searchTerm";
        $pst = $dbcon->prepare($sql);
        $searchTerm = "%" . $searchTerm . "%";
        $pst->bindParam(':searchTerm', $searchTerm);
        $pst->execute();
    
        return $pst->fetchAll(PDO::FETCH_OBJ);
    }
    public function getSurveysByCategory($dbcon, $categoryId) {
        $query = "SELECT * FROM surveys WHERE category_id = :categoryId";
        $pdostm = $dbcon->prepare($query);
        $pdostm->bindParam(':categoryId', $categoryId);
        $pdostm->execute();

        return $pdostm->fetchAll(PDO::FETCH_OBJ);
    }
    public function searchVisibleSurveysByTitle($db, $title) {
        $sql = "SELECT * FROM surveys WHERE title LIKE :title AND visible = 1";
        $pst = $db->prepare($sql);
        $searchTerm = "%" . $title . "%";
        $pst->bindParam(':title', $searchTerm);
        $pst->execute();

        return $pst->fetchAll(PDO::FETCH_OBJ);
    }

    // Funkcija koja vraća sve vidljive ankete
    public function listAllVisibleSurveys($db) {
        $sql = "SELECT * FROM surveys WHERE visible = 1";
        $pst = $db->prepare($sql);
        $pst->execute();

        return $pst->fetchAll(PDO::FETCH_OBJ);
    }
    
    
    
    
    
}
?>
