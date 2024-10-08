<?php

///Mihajlo Eskic

class Database
{
    

    // DB configs to work locally
    private static $user = 'root';
    private static $password = '';
    private static $dbname = 'zavrsni1';
    private static $dsn = 'mysql:host=localhost;dbname=zavrsni1';

    private static $dbcon;

    private function __construct()
    {
    }

    //get pdo connection
    public static function getDb()
    {
        if (!isset(self::$dbcon)) {

            try {
                self::$dbcon = new PDO(self::$dsn, self::$user, self::$password);
            } catch (PDOException $e) {
                $msg = $e->getMessage();
                //include "customerror.php";
                exit();

            }
        }
        return self::$dbcon;
    }
    public static function connectDB(){
        if(!isset(self::$dbcon)){
            try {
                self::$dbcon = new PDO(self::$dsn, self::$user, self::$password);
                self::$dbcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$dbcon->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            } catch (PDOException $e) {
                $msg = $e->getMessage();
                echo $msg;
            }
        }
        return self::$dbcon;
    }
    public static function checkUserCreds($email, $password){
        $stmt = self::$dbcon->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);
    
        if ($user && password_verify($password, $user->password)) {
            if ($user->is_verified == 1) {
                return $user;
            } else {
                // Vraćamo objekt sa ID-jem korisnika ali sa oznakom da nije verifikovan
                return (object) [
                    'is_verified' => 0,
                    'id' => $user->id
                ];
            }
        }
        return null;
    }
    

    public static function registerUser($fname, $lname, $email, $password){
        $query = 'INSERT INTO users (fname, lname, email, password, isAdmin)
                    VALUES (?,?,?,?,?);';
        $newUser = self::$dbcon->prepare($query);
        $password = password_hash($password, PASSWORD_DEFAULT);
        $newUser->execute([$fname, $lname, $email, $password, 0]);
        if($newUser)
            return self::$dbcon->lastInsertId();
        
        return false;
    }

    public static function registerAdmin($fname, $lname, $email, $password){
        $query = 'INSERT INTO users (fname, lname, email, password, isAdmin)
                    VALUES (?,?,?,?,?);';
        $newUser = self::$dbcon->prepare($query);
        $password = password_hash($password, PASSWORD_DEFAULT);
        $newUser->execute([$fname, $lname, $email, $password, 1]);
        if($newUser)
            return self::$dbcon->lastInsertId();
        
        return false;
    }

    public static function getSurvey($surveyId){
        $survey = self::$dbcon->prepare("SELECT * FROM surveys WHERE id = ?");
        $survey->execute([$surveyId]);
        $survey = $survey->fetch();
        return $survey;
    }

    public static function getCategoryName($categoryId){
        $category = self::$dbcon->prepare("SELECT * FROM categories WHERE id = ?");
        $category->execute([$categoryId]);
        $category = $category->fetch();
        return $category->name;
    }

    public static function getUserName($userId){
        $user = self::$dbcon->prepare("SELECT * FROM users WHERE id = ?");
        $user->execute([$userId]);
        $user = $user->fetch();
        return $user->fname . " " . $user->lname;
    }

    public static function getQuestions($surveyId){
        $questions = self::$dbcon->prepare("SELECT * FROM questions WHERE survey_id = ?");
        $questions->execute([$surveyId]);
        $questions = $questions->fetchAll();
        return $questions;
    }

    public static function insertAnswer($answer, $question_id, $user_id){
        $newAnswer = self::$dbcon->prepare("INSERT INTO answers (answer, question_id, user_id) VALUES (?, ?, ?);");
        $newAnswer->execute([$answer, $question_id, $user_id]);
        return $newAnswer;
    }

}
?>