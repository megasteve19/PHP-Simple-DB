<?php
    /** 
     * MySQL library for less complexity.
     * 
     * @author megasteve19
     * @link https://github.com/megasteve19/PHP-Simple-DB
     * @version 1.1.0
     */
    class Simple_DB
    {
        private $ConnectionConfig =
        [
            "Host"=>"",
            "Username"=>"",
            "Password"=>"",
            "Database"=>""
        ];

        private $TableName = "";


        private function Connection()
        {
            $Connection = new mysqli($this->ConnectionConfig["Host"], $this->ConnectionConfig["Username"], $this->ConnectionConfig["Password"], $this->ConnectionConfig["Database"]);

            if(!$Connection->connect_error)
            {
                return $Connection;
            }
            else
            {
                die("Error on connect");
            }
        }
        
        /**
         * @param string $Columns
         * @param string $Where
         * @return mixed
         * 
         * @since 1.0.0
         */
        public function Table($Columns = null, $Where = null)
        {
            $Query = $this->Build("SELECT", ["Columns"=>$Columns, "Where"=>$Where]);

            $Connection = $this->Connection();
            $Result = $Connection->query($Query)->fetch_all(MYSQLI_ASSOC);
            $Connection->close();

            if($Result)
            {
                return $Result;
            }
            else
            {
                return false;
            }
        }

        /**
         * @param string $Columns
         * @param string $Where
         * @return mixed
         * 
         * @since 1.1.0
         */
        public function Row($Columns, $Where)
        {
            $Table = $this->Table($Columns, $Where);

            if($Table)
            {
                return $Table[0];
            }
            else
            {
                return false;
            }
        }

        /**
         * @param string $Values
         * @return mixed
         * 
         * @since 1.0.0
         */
        public function Insert($Values = null)
        {
            $Query = $this->Build("INSERT", ["Values"=>$Values]);

            $Connection = $this->Connection();
            $Connection->query($Query);
            $Connection->close();

            if(!$Connection->error)
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * @param string $Set
         * @param string $Where
         * @return mixed
         * 
         * @since 1.0.0
         */
        public function Update($Set = null, $Where = null)
        {
            $Query = $this->Build("UPDATE", ["Set"=>$Set, "Where"=>$Where]);

            $Connection = $this->Connection();
            $Connection->query($Query);
            $Connection->close();

            if(!$Connection->error)
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * @param string $Where
         * @return mixed
         * 
         * @since 1.0.0
         */
        public function Delete($Where)
        {
            $Query = $this->Build("DELETE", ["Where"=>$Where]);

            $Connection = $this->Connection();
            $Connection->query($Query);
            $Connection->close();

            if(!$Connection->error)
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * @param string $Search
         * @param string $In
         * @return mixed
         * 
         * @since 1.1.0
         */
        public function Search($Search, $In = null)
        {
            $Query = $this->Build("SEARCH", ["In"=>$In, "Search"=>$Search]);

            $Connection = $this->Connection();
            $Result = $Connection->query($Query)->fetch_all(MYSQLI_ASSOC);
            $Connection->close();

            if($Result)
            {
                return $Result;
            }
            else
            {
                return false;
            }
        }

        public function Build($Type, $Array)
        {
            $Array = $this->Extract($Array);
            $Query = "";

            if($Type == "SELECT")
            {
                $Query .= "SELECT ";

                if(!empty($Array["Columns"]))
                {
                    $this->CheckColumns($Array["Columns"]);
                    $Builded = $this->BuildColumns($Array["Columns"]);
                    $Query .= "$Builded ";
                }
                else
                {
                    $Query .= "* ";
                }

                $Query .= "FROM {$this->TableName} ";

                if(!empty($Array["Where"]))
                {
                    $this->CheckColumns($Array["Where"]["Columns"]);
                    $Builded = $this->BuildEquation($Array["Where"]["Columns"], $Array["Where"]["Values"], "AND");
                    $Query .= "WHERE $Builded;";
                }
            }
            else if($Type == "INSERT")
            {
                if(!empty($Array["Values"]))
                {
                    $this->CheckColumns($Array["Values"]["Columns"]);
                    $Columns = $this->BuildColumns($Array["Values"]["Columns"]);
                    $Values = $this->BuildCommaVariables($Array["Values"]["Columns"], $Array["Values"]["Values"]);
                    $Query = "INSERT INTO {$this->TableName}($Columns) VALUES($Values)";
                }
            }
            else if($Type == "UPDATE")
            {
                $Query .= "UPDATE {$this->TableName} ";

                if(!empty($Array["Set"]))
                {
                    $this->CheckColumns($Array["Set"]["Columns"]);
                    $Builded = $this->BuildEquation($Array["Set"]["Columns"], $Array["Set"]["Values"], ",");
                    $Query .= "SET $Builded ";
                }

                if(!empty($Array["Where"]))
                {
                    $this->CheckColumns($Array["Where"]["Columns"]);
                    $Builded = $this->BuildEquation($Array["Where"]["Columns"], $Array["Where"]["Values"], "AND");
                    $Query .= "WHERE $Builded;";
                }
            }
            else if($Type == "DELETE")
            {
                $Query .= "DELETE FROM {$this->TableName} ";

                if(!empty($Array["Where"]))
                {
                    $this->CheckColumns($Array["Where"]["Columns"]);
                    $Builded = $this->BuildEquation($Array["Where"]["Columns"], $Array["Where"]["Values"], "AND");
                    $Query .= "WHERE $Builded;";
                }
            }
            else if($Type == "SEARCH")
            {
                $Query .= "SELECT * FROM {$this->TableName} WHERE ";

                if(!empty($Array["In"]))
                {
                    $this->CheckColumns($Array["In"]);
                    $Builded = $this->BuildColumns($Array["In"]);
                    $Query .= "CONCAT($Builded) ";
                }
                else
                {
                    $Query .= "CONCAT(*) ";
                }

                $Query .= "LIKE '%$Array[Search]%'";
            }

            return $Query;
        }

        private function EqualArrayCount($First, $Second)
        {
            if(count($First) == count($Second))
            {
                return;
            }
            else
            {
                throw new Exception("Column and value count must equal", 1003);
            }
        }

        private function BuildColumns($Columns)
        {
            $String = "";

            foreach($Columns as $Key => $Value)
            {
                $String .= "$Value";

                if(array_key_last($Columns) != $Key)
                {
                    $String .= ", ";
                }
            }

            return $String;
        }

        private function BuildEquation($Columns, $Values, $Separator)
        {
            $String = "";

            try 
            {
                $this->EqualArrayCount($Columns, $Values);
            }
            catch(Throwable $th)
            {
                $this->ExceptionHandler($th);
            }

            foreach ($Columns as $Key => $Value)
            {
                $String .= "$Value = " . $this->FormatVariable($Value, $Values[$Key]);

                if(array_key_last($Columns) != $Key)
                {
                    $String .= " $Separator ";
                }
            }

            return $String;
        }

        private function BuildCommaVariables($Columns, $Values)
        {
            $String = "";

            try 
            {
                $this->EqualArrayCount($Columns, $Values);
            }
            catch(Throwable $th)
            {
                $this->ExceptionHandler($th);
            }

            foreach ($Columns as $Key => $Value)
            {
                $String .= $this->FormatVariable($Value, $Values[$Key]);

                if(array_key_last($Columns) != $Key)
                {
                    $String .= ", ";
                }
            }

            return $String;
        }

        private function FormatVariable($Column, $Value)
        {
            $Connection = $this->Connection();

            $Type = $Connection->query("SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '{$this->TableName}' AND COLUMN_NAME = '$Column'")->fetch_all(MYSQLI_ASSOC)[0]["DATA_TYPE"];
            $Connection->close();
            $String = "";

            $Numerics = ["bit", "tinyint", "smallint", "mediumint", "int", "integer", "bigint", "float", "double", "decimal", "dec"];
            $Strings = ["char", "varchar", "binary", "varbinary", "tinyblob", "tinytext", "text", "blob", "mediumtext", "mediumblob", "longtext", "longblob"];
            $DateTime = ["date", "datetime", "timestamp", "time", "year"];

            if(in_array($Type, $Numerics))
            {
                $String = $Value;
            }
            else if(in_array($Type, $Strings) || in_array($Type, $DateTime))
            {
                $Value = str_replace("'", "''", $Value);
                $String = "'$Value'";
            }

            return $String;
        }

        public function Extract($Array)
        {
            foreach ($Array as $Key => $Value)
            {
                if($Array[$Key] != "")
                {
                    if($Key == "Columns" || $Key == "In")
                    {
                        $Array[$Key] = $this->ParseColumns($Array[$Key]);
                    }
                    else if($Key == "Where" || $Key == "Values" || $Key == "Set")
                    {
                        $Split = $this->SplitSlash($Array[$Key]);
    
                        $Array[$Key] = [];
                        $Array[$Key]["Columns"] = $this->ParseColumns($Split[0]);
                        $Array[$Key]["Values"] = $this->ParseValues($Split[1]);
                    }  
                }
                else
                {
                    $Array[$Key] == null;
                }
            }

            return $Array;
        }

        private function ParseColumns($String)
        {
            $String = explode(",", $String);

            foreach ($String as $Key => $Value)
            {
                $String[$Key] = trim($Value);
            }

            return $String;
        }

        private function ParseValues($String)
        {
            $String = str_getcsv($String, ",", "'");

            foreach ($String as $Key => $Value)
            {
                $String[$Key] = trim($Value);
            }

            return $String;
        }

        private function SplitSlash($String)
        {
            $String = explode("/", $String, 2);

            return $String;
        }

        public function ExceptionHandler($Exception)
        {
            $UserCallFunctions = ["SDB", "Table"];

            $Message = $Exception->getMessage();
            $ErrorCode = $Exception->getCode();
            $StackTraceArray = $Exception->getTrace();
            $StackTraceString = "";

            foreach ($StackTraceArray as $Key => $Value)
            {
                if(in_array($Value["function"], $UserCallFunctions))
                {
                    $StackTraceString .= "<b>#$Key $Value[function](function) on line $Value[line] at $Value[file]</b><br>\n";
                }
                else
                {
                    $StackTraceString .= "<b>#$Key</b> $Value[function](function) on <b>line $Value[line]</b> at $Value[file]<br>\n";
                }
            }

            echo "<h2>SDB|Error</h2><b>Message:</b>$Message<br>\n<b>Error code:</b> $ErrorCode<br>\n<b>Stack trace:</b><br>\n$StackTraceString";
            exit();
        }

        public function IsTableExist($TableName)
        {
            $Connection = $this->Connection();
            $TableName = strtolower($TableName);

            $Result = $Connection->query("SHOW TABLES")->fetch_all(MYSQLI_NUM);
            $Connection->close();

            foreach ($Result as $Key) 
            {
                if(in_array($TableName, $Key))
                {
                    return;
                }
            }

            throw new Exception("There is no table named '$TableName' in database", 1001);
        }

        public function IsColumnsExist($Columns)
        {
            $Connection = $this->Connection();
            $NotExist = [];
            $NotExistString = "";

            $Result = $Connection->query("DESCRIBE {$this->TableName}")->fetch_all(MYSQLI_ASSOC);
            $Connection->close();

            foreach($Columns as $Key => $Value)
            {
                foreach($Result as $RKey => $RValue)
                {
                    if($Value == $RValue["Field"])
                    {
                        $Exist = true;
                        break;
                    }
                    else
                    {
                        $Exist = false;
                    }
                }

                if(!$Exist)
                {
                    array_push($NotExist, $Value);
                }
            }

            foreach ($NotExist as $Key => $Value)
            {
                $NotExistString .= "'$Value'";

                if($Key != array_key_last($NotExist))
                {
                    $NotExistString .= ", ";
                }
            }

            if(!empty($NotExist))
            {
                throw new Exception("There are no columns specified as $NotExistString in '{$this->TableName}' table", 1002);
            }
        }

        private function CheckColumns($Columns)
        {
            try
            {
                $this->IsColumnsExist($Columns);
            }
            catch(Throwable $th)
            {
                $this->ExceptionHandler($th);
            }
        }

        public function __construct($TableName)
        {
            try
            {
                $this->ConnectionConfig = $GLOBALS["SimpleDB_ConnectConf"];
                $this->IsTableExist($TableName);
                $this->TableName = $TableName;
            }
            catch (Throwable $th)
            {
                $this->ExceptionHandler($th);
            }
        }
    }

    /**
     * @param mixed $var
     * @return mixed
     * 
     * @since 1.0.0
     */
    function SDB($var)
    {
        if(gettype($var) == "array")
        {
            $GLOBALS["SimpleDB_ConnectConf"] = $var;
        }
        else if(gettype($var) == "string")
        {
            return new Simple_DB($var);
        }
    }
?>
