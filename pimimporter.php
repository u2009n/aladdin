<?php


   require_once('db_connection.php'); // call db connection to Datenbank

        $mainFieldsName=null;
        $preisFiledsName=null;
       
  
   


                        // To get the columns name from table map_ex_out and arrange them 

                function get_fields($sql,$conn){    

                    
                              $index=0;
                                $result = mysqli_query($conn,$sql);
                                    if(mysqli_num_rows($result) > 0){
                                      while($row = mysqli_fetch_array($result)){
                                                if($index > 0){
                                                  $fields[]= ($row['Field']);
                                                }  
                                              $index+=1;    
                                        }
                                        
                                    }  

                      return $fields;          
                              
                

                }


                // To remove prifex zgi

                function get_Filter_zg($_fields,$tb_index){


                $len=strlen('zg'.$tb_index.'_'); 
                        foreach($_fields as $field){
                            $fields[]=is_numeric(substr($field,$len)) ? 'm_'.substr($field,$len) : substr($field,$len);
                                
                        }
                        return $fields;

                }

                // Get qeuray statement

                  function get_qeuray($fields){

                      $_fields=null;$index=0;
                        for($index=0;$index < count($fields);$index++){
                            if($index==count($fields)-1){
                                $_fields.=$fields[$index] .'  text ';
                            }else{
                              $_fields.=$fields[$index] .'  text ,';
                            } 
                        }     

                        return $_fields;

                  }


                  // To DROP old tables 


                function drop_table($conn,$tb_index){   
                        $sql = "DROP TABLE IF EXISTS map_ex_out_zg".$tb_index."";
                        $conn->query($sql);

                }

                  
                // To Create Newc tables 

                function Create_Tables($fields,$tb_index,$conn){   
                 
                 
                  $path = "localhost/aladdin/public/act=pimimporter&I=zg".$tb_index;
                 
                    $_fields=null;
                    $_fields=get_qeuray($fields);
                    drop_table($conn,$tb_index);

                      $sql = "CREATE TABLE map_ex_out_zg".$tb_index." (
                          id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,".  $_fields."
                          )";
                        $conn->query($sql);
                        if (!is_dir($path)) {
                          mkdir($path, 0777, true);
                      }

                    
                }


                function get_implode($fields){

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
                

                    
                    //Insert Data in New Tables

                function Insert_data($alt_fields,$neu_fields,$tb_index,$conn){    
                
                    $table='map_ex_out';
                    $new_table='map_ex_out_zg';
                    $sql_insert='';


                    $alt_tb_fields=get_implode($alt_fields);
                    $neu_tb_fields=get_implode($neu_fields);
                  
                  

                          $qeuray="SELECT   $alt_tb_fields  FROM $table";
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
                                    $sql_insert="INSERT INTO $new_table$tb_index ($neu_tb_fields) VALUES $values";
                                    $conn->query($sql_insert);

                              

                            }  
                      
                  

                }









                //*************** Main Code *************************************/

       for ($tb_index=1;$tb_index<=5;$tb_index++ ){ 

                
                  $mainFieldsName="SHOW columns from map_ex_out where field not like 'zg%'"; 
                  $preisFiledsName='SHOW columns from map_ex_out where field like';
                  $preisFiledsName.=" 'zg".$tb_index."%'";


                        $fields=get_fields($mainFieldsName,$conn);
                        $prisFields=get_fields($preisFiledsName,$conn);
                        $zg_fields=get_Filter_zg($prisFields,$tb_index);


                        $_fields= array_merge($fields,$zg_fields);
                        Create_Tables($_fields,$tb_index,$conn); 


                        $alt_tb_fields= array_merge($fields,$prisFields);  
                        Insert_data($alt_tb_fields,$_fields,$tb_index,$conn);
                   
       }                 
         echo '<script>alert("die Tabellen wurden erfolgreich erstellt")</script>';

            CloseCon($conn);  
?>





<link rel='stylesheet' type='text/css' href='style.css'>    <!--Css file -->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>   <!--jQuray  file -->



<div class="container">
    <h1>Tabellen-Generator</h1>
     <div class="content">
         
     <form  method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" >

         <input type="hidden" name ="i" disabled>
         <input type="hidden" name="act" value="pimimporter" disabled>
         </form>   
     </div>   
     
   