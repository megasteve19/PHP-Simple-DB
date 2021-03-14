<?php
    class Simple_DB
    {
        private $ConnectionConfig =
        [
            "Host"=>"localhost",
            "Username"=>"root",
            "Password"=>"kadir",
            "Database"=>"epano"
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
        
        public function Table($Columns = null, $Where = null)
        {
            $Connection = $this->Connection();

            $Array["Columns"] = $Columns;
            $Array["Where"] = $Where;
            $Query = $this->BuildQuery("Get", $Array);

            $Result = $Connection->query($Query);

            $Connection->close();

            if($Result)
            {
                return $Result->fetch_all(MYSQLI_ASSOC);
            }
            else
            {
                return false;
            }
        }

        public function Insert($Values = null)
        {
            $Connection = $this->Connection();

            $Query = $this->BuildQuery("Insert", $Values);

            if($Connection->query($Query))
            {
                return true;
            }
            else
            {
                return false;
            }

            $Connection->close();
        }

        public function Update($Set = null, $Where = null)
        {
            $Connection = $this->Connection();

            $Array["Set"] = $Set;
            $Array["Where"] = $Where;
            $Query = $this->BuildQuery("Set", $Array);

            if($Connection->query($Query))
            {
                return true;
            }
            else
            {
                return false;
            }

            $Connection->close();
        }

        public function Delete($Where)
        {
            $Connection = $this->Connection();

            $Query = $this->BuildQuery("Delete", $Where);

            if($Connection->query($Query))
            {
                return true;
            }
            else
            {
                return false;
            }

            $Connection->close();
        }

        private function BuildQuery($Type, $Array)
        {
            switch ($Type)
            {
                case 'Get':
                    $Query = "SELECT ";

                    if(!empty($Array["Columns"]))
                    {
                        $Query .= $this->GetColumns($Array["Columns"]) . "FROM {$this->TableName} ";
                    }
                    else
                    {
                        $Query .= "* FROM {$this->TableName} ";
                    }

                    if(!empty($Array["Where"]))
                    {
                        $Query .= "WHERE " . $this->SetColumns($Array["Where"], "AND");
                    }

                    return trim($Query) . ";";
                    break;
                
                case "Insert":
                    $Query = "INSERT INTO ";

                    if(!empty($Array))
                    {
                        $Query .= "{$this->TableName}(" . trim($this->GetColumns($this->SeperateSlash($Array)[0])) . ") VALUES(" . trim($this->GetColumns($this->SeperateSlash($Array)[1], true)) . ")";
                    }

                    return $Query;
                    break;
                
                case "Set":
                    $Query = "UPDATE {$this->TableName} ";

                    if(!empty($Array["Set"]))
                    {
                        $Query .= "SET " . $this->SetColumns($Array["Set"], ",");
                    }

                    if(!empty($Array["Where"]))
                    {
                        $Query .= "WHERE " . $this->SetColumns($Array["Where"], "AND");
                    }

                    return trim($Query) . ";";
                    break;
                case "Delete":
                    $Query = "DELETE FROM {$this->TableName} ";

                    if(!empty($Array))
                    {
                        $Query .= "WHERE " . $this->SetColumns($Array, "AND");
                    }

                    return trim($Query) . ";";
                    break;
            }
        }

        private function GetColumns($String, $IsVar = false)
        {
            $Columns = $this->SeparateComma($String);
            $Q = "";

            if($IsVar)
            {
                foreach ($Columns as $Key => $Value)
                {
                    if(Count($Columns) - 1 == $Key)
                    {
                        $Q .= $this->FormatSQLVariable($Value) . " ";
                    }
                    else
                    {
                        $Q .= $this->FormatSQLVariable($Value) . ", ";
                    }
                }
            }
            else
            {
                foreach ($Columns as $Key => $Value)
                {
                    if(Count($Columns) - 1 == $Key)
                    {
                        $Q .= "$Value ";
                    }
                    else
                    {
                        $Q .= "$Value, ";
                    }
                }
            }

            return $Q;
        }

        private function SetColumns($String, $Separator)
        {
            $Array["Columns"] = $this->SeparateComma($this->SeperateSlash($String)[0]);
            $Array["Values"] = $this->SeparateComma($this->SeperateSlash($String)[1]);

            $Q = "";

            foreach ($Array["Columns"] as $Key => $Value)
            {
                if(Count($Array["Columns"]) - 1 == $Key)
                {
                    $Q .= "$Value = " . $this->FormatSQLVariable($Array['Values'][$Key]) . " ";
                }
                else
                {
                    $Q .= "$Value = " . $this->FormatSQLVariable($Array['Values'][$Key]) . " $Separator ";
                }
            }

            return $Q;
        }

        private function SeparateComma($String)
        {
            $String = explode(",", $String);

            foreach($String as $Key => $Value)
            {
                $String[$Key] = trim($Value);
            }

            return $String;
        }

        private function SeperateSlash($String)
        {
            return explode("/", $String);
        }

        private function FormatSQLVariable($String)
        {
            if(is_numeric($String))
            {
                return $String;
            }
            else
            {
                return "'$String'";
            }
        }

        public function __construct($TableName)
        {
            $this->TableName = $TableName;
        }
    }

    function SDB($TableName)
    {
        return new Simple_DB($TableName);
    }
?>
