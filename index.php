<?php

//Bug report
ini_set('display_errors', 'On');
error_reporting(E_ALL);


define('DATABASE', 'wk45');
define('USERNAME', 'wk45');
define('PASSWORD', 'AmCb81UGG');
define('CONNECTION', 'sql1.njit.edu');


class dbConn{
    
    protected static $db;
    
    public function __construct() {
        
        try {
            
            self::$db = new PDO( 'mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD );
            self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            echo 'Connected successfully<br>';

        } catch (PDOException $e) {
            
            echo "Connection Error: " . $e->getMessage();

        }

    }
    
    public static function getConnection() {
        
        if (!self::$db) {
            
            new dbConn();

        }
        
        return self::$db;

    }
}


class collection {

    static public function create() {

        $model = new static::$modelName;
        return $model;

    }
       
    public  function findAll() {

         $db = dbConn::getConnection();
         $tableName = get_called_class();
         $sql = 'SELECT * FROM ' . $tableName;
         $statement = $db->prepare($sql);
         $statement->execute();
            
         $class = static::$modelName;
         $statement->setFetchMode(PDO::FETCH_CLASS, $class);
        
         $recordsSet =  $statement->fetchAll();
         return $recordsSet;
    
    }

    public  function findOne($id) {
      
         $db = dbConn::getConnection();
         $tableName = get_called_class();
         $sql = 'SELECT * FROM ' . $tableName . ' WHERE id =' . $id;
         $statement = $db->prepare($sql);
         $statement->execute();
         $class = static::$modelName;
         $statement->setFetchMode(PDO::FETCH_CLASS,$class);
         $recordsSet  =  $statement->fetchAll();
         return $recordsSet;
    
    }
}      
   




class accounts extends collection {

    protected static $modelName='accounts';

}


class todos extends collection {

    protected static $modelName='todos';

}


class model {

    static $columnString;
    static $valueString;
       
    public function save() {
             
        if (static::$id == '') {

             $db=dbConn::getConnection();
             $array = get_object_vars($this);
             static::$columnString = implode(', ', $array);
             static::$valueString = implode(', ',array_fill(0,count($array),'?'));
             $sql = $this->insert();
             $stmt=$db->prepare($sql);
             $stmt->execute(static::$data);

        } else  {
               
             $db=dbConn::getConnection();
             $array = get_object_vars($this);
             $sql = $this->update();
             $stmt=$db->prepare($sql);
             $stmt->execute();
        
         }

    }

    private function insert() {
               
         $sql = "INSERT INTO ".static::$tableName." (". static::$columnString . ") Values(". static::$valueString . ") ";
         return $sql;
      
    }
 
    private function update() {
         $sql = "UPDATE ".static::$tableName. " SET ".static::$columnUpdate."='".static::$newInfo."' WHERE id=".static::$id;
         return $sql;
          
    }
                                       
    public function delete() {
                
         $db=dbConn::getConnection();
         $sql = 'Delete From '.static::$tableName.' WHERE id='.static::$id;
         $stmt=$db->prepare($sql);
         $stmt->execute();

    }
}




class table {

    static  function createTable($result) {
        
        echo '<table>';
        echo "<table cellpadding='10px' border='2px' style='border-collapse:collapse' text-align :'center' width ='100%'white-space : nowrap'font-''weight:bold'>";
            
        foreach($result as $column) {
            
            echo '<tr>';
            foreach($column as $row) {
                  
            echo '<td>';
            echo $row;
            echo '</td>';
                  
            }

         echo '</tr>';

         }

    echo '</table>';

    }
}


 echo '<h2>Select all from Accounts Table  </h2>';
 $records = accounts::create();
 $result = $records->findAll();
 table::createTable($result);  
 echo '<br>';
       

 echo '<h2>Select ID=10 from Accounts Table</h2>';
 $result= $records->findOne(10);
 table::createTable($result); 
 echo '<br>';


 echo '<h2>Select all from Todos Table </h2>';
 $records = todos::create();
 $result= $records->findAll(); 
 table::createTable($result);        
 echo '<br>';

         
 echo '<h2>Select ID=2  from Todos Table<h2>';
 $result= $records->findOne(2);
 table::createTable($result);        

?>
