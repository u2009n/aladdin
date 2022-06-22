<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="./sweetalert/sweetalert2.all.min.js"></script>
<    <script src="./sweetalert/jquery-3.6.0.min.js"></script>
</head>
<body>
    




<?php


   require_once('db_connection.php'); // call db connection to Datenbank
   $url="http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; // Get the Url


//******************************************************************************************************************************//
                function get_fields($result,$validZgFilelds){       // To get the columns name from table map_ex_out and arrange them 


                              $index=0;
                              if($validZgFilelds<>true){  
                                  while($row = mysqli_fetch_array($result)){  //returen the main fields
                                        if($index > 0){
                                            $fields[]= ($row['Field']);
                                          }  
                                        $index+=1;    
                                    }
                                }else{
                                 while($row = mysqli_fetch_array($result)){  //returen the zg fields
                                        $fields[]= ($row['Field']);

                                     }    
                              
                                } 
                              
                                return $fields;       

                }


//******************************************************************************************************************************//
                function get_Filter_zg($_fields,$tb_index){    // To remove prifex zgi and replace it with m_

              
                  $len=strlen('zg'.$tb_index.'_'); 
                          foreach($_fields as $field){
                              $fields[]=is_numeric(substr($field,$len)) ? 'm_'.substr($field,$len) : substr($field,$len);
                          }
                          return $fields;

                }


//******************************************************************************************************************************//
                  function get_qeuray($fields){   // Arrange the Fields in order to Create new Table 

                      $_fields=null;$index=0;$comma =", ";

                        for($index=0;$index < count($fields);$index++){
                            if($index==count($fields)-1){
                                $_fields.=$fields[$index] .'  text ';  //Arrange the fields with thier data-type 
                            }else{
                              $_fields.=$fields[$index] .'  text '.$comma; // eadd commas to feild list
                            } 
                        }     

                        return $_fields;

                  }


//******************************************************************************************************************************//
                function drop_table($conn,$tb_index){    // To DROP old tables 
                        $sql = "DROP TABLE IF EXISTS map_ex_out_zg".$tb_index."";
                        $conn->query($sql);

                }
                  

//******************************************************************************************************************************//
                function Create_Tables($fields,$tb_index,$conn){    // To Create Newc tables 

                    $_fields=null;
                    $_fields=get_qeuray($fields);
                    drop_table($conn,$tb_index);

                      $sql = "CREATE TABLE map_ex_out_zg".$tb_index." (
                          id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,".  $_fields."
                          )";
                        $conn->query($sql);
                    
                }


  //******************************************************************************************************************************//              
                function get_implode($fields){     // get implode the fields from Array to query statmment

                  $qeuray=null;  $index=0;  $comma=',';
                    foreach($fields as $field){
                        if($index==count($fields)-1){
                          $qeuray.=$field;
                        }else{
                          $qeuray.=$field.$comma;
                        } 
                      $index+=1;    
                    }
                  
                return $qeuray;
                }


    //******************************************************************************************************************************//         
             
    function Insert_data($alt_fields,$neu_fields,$tb_index,$conn){      //Insert Data in New Tables
                
                    $table='map_ex_out';
                    $neu_table='map_ex_out_zg';
                    $sql_insert='';


                   $alt_tb_fields=get_implode($alt_fields);//implode the fields with comma
                    $neu_tb_fields=get_implode($neu_fields); //implode the fields with comma
                  
                          $qeuray="SELECT   $alt_tb_fields  FROM $table";  //Select the Data from Old Table map_ex_out
                          $result = mysqli_query($conn,$qeuray);

                            $count = count($alt_fields); $comma =", ";
                            $values='';

                            if(mysqli_num_rows($result) > 0){
                              $counter= $row = mysqli_num_rows($result); $index_rec=0;;
                                    while($row = mysqli_fetch_array($result)){
                                        
                                        $index = 0;    $value='';
                                
                                        foreach($alt_fields as $feild){
                                              $value .= "'".$row[$feild]."'";
                                                        
                                              if($index < $count -1)
                                                $value .= $comma; //add commas to feild list
                                                $index++;
                                        }
                                        $values.="($value)"; 
                                        if($index_rec < $counter -1)
                                                $values .= $comma; //add commas to feild list
                                                $index_rec++;
                                                                  
                                
                                    }
                                    $sql_insert="INSERT INTO $neu_table$tb_index ($neu_tb_fields) VALUES $values"; //Insert Data in New Table
                                    $conn->query($sql_insert);


                            }  
                  

                }


    //*******************************************************    Main Code   **************************************************************************//
    
    $mainFieldsName=null;$preisFiledsName=null;
    $tb_index=0; $max=0; $index=0; $_validZgFields=false;

           if (isset($_GET['i'])) {
                    
                        if(is_numeric(substr($_GET['i'],2))){

                              $index=substr($_GET['i'],2); $max=substr($_GET['i'],2);  // To get the Tablenummer when used als  indvidu

                        }else{ 
                            // echo '<script>alert("die Adresse oder die Tablenummer stimmt nicht")</script>';
                              ?>

                            <script>

                              Swal.fire({
                                icon: 'error',
                                title: 'Warnung...',
                                text: 'die Adresse oder die Tablenummer stimmt nicht',
                              })
                            </script>
                                            
                  <?php }
      
            }else{

              $index=1; $max=5;
            }    

                   if($index<>0 && $max<>0){
                         for ($tb_index=$index;$tb_index<=$max;$tb_index++ ){ 

                            
                              $mainFieldsName="SHOW columns from map_ex_out where field not like 'zg%'";  // Select the fields names that have no profex zg
                              $preisFiledsName='SHOW columns from map_ex_out where field like';           // Select the fields names that have zg prifex
                              $preisFiledsName.=" 'zg".$tb_index."%'";


                                    $result = mysqli_query($conn,$mainFieldsName); // To get Main fields 
                                    $fields=get_fields($result,'');

                                    
                                        $result = mysqli_query($conn,$preisFiledsName);   // To get ZG fields
                                        if(mysqli_num_rows($result) > 0){  

                                                    $_validZgFields=true;
                                                      $prisFields=get_fields($result,$_validZgFields);
                                                        $zg_fields=get_Filter_zg($prisFields,$tb_index);


                                                           $_fields= array_merge($fields,$zg_fields);
                                                              Create_Tables($_fields,$tb_index,$conn); 


                                                           $alt_tb_fields= array_merge($fields,$prisFields);
                                                        Insert_data($alt_tb_fields,$_fields,$tb_index,$conn);

                                                      if (!is_dir('act=pimimporter&l=zg'.$tb_index)) {  // To create new director
                                                    mkdir('act=pimimporter&l=zg'.$tb_index, 0777, true);
                                                 }


                                           }else{
                                          echo '<script>alert("Es gibt keine Zielgruppe mit dieser Nummer")</script>';
                                        }  

                          }  
                         if($_validZgFields !=false) 

                       echo '<script>alert("die Tablle oder  Tabellen wurden erfolgreich erstellt")</script>';

                   }    
   
    //*******************************************************    End Main Code   **************************************************************************//
    ?>




<!-----------------------------------------------------------    HTML  Part     ------------------------------------------------------------------------------>

<link rel='stylesheet' type='text/css' href='style.css'>    <!--Css file -->






<div class="container">
    <h1>Tabellen-Generator</h1>
     <div class="content">
         
     <form  method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
         <input type="hidden" name="i"> 
         <input type="hidden" name ="pop" value="die Tabellen wurden erfolgreich erstellt" id="pop" >
         <input type="hidden" name="act" value="pimimporter" disabled>
         </form>   
     </div>   
     
</div>



		


