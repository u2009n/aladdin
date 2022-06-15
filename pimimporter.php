


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
                        $sql = "DROP TABLE IF EXISTS map_ex_out".$tb_index."";
                        $conn->query($sql);

                }

                  
                // To Create Newc tables 

                function Create_Tables($fields,$tb_index,$conn){   

                    $_fields=null;
                    $_fields=get_qeuray($fields);
                    drop_table($conn,$tb_index);

                      $sql = "CREATE TABLE map_ex_out".$tb_index." (
                          id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,".  $_fields."
                          )";
                        $conn->query($sql);
                    
                }



                

                    
                    //Insert Data in New Tables

                function Insert_data($alt_fields,$neu_fields,$tb_index,$conn){    
                
                    $table='map_ex_out';
                    $sql_insert='';


                    $_fields=implode(',',$alt_fields);
                    $_neu_tb_fields=implode(',',$neu_fields);
                  
                  
                  

                          $qeuray="SELECT   $_fields  FROM $table";
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
                                    $sql_insert="INSERT INTO $table$tb_index ($_neu_tb_fields) VALUES $values";
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
     
    <script>

      $(document).ready(function(){
        $('h1').css('color','#069');
        $('h1').css(' background-color','#fcfcfc');
      })
    </script>
</div>  



		


