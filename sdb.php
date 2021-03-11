<?php
    class SDB
    {
        const SQLERROR = "<b>Something wrong with your SQL syntax!</b>";

        use Connection;
        use DataTable;
        use Check;
    }

    trait Connection
    {
        public $Host = "localhost";
        public $Username = "";
        public $Password = "";
        public $Database = "";

        public function LoadConfig($File)
        {
            if(!file_exists($File))
            {
                exit("<h1>ERROR</h1><b>File not exists! Please enter valid file directory.</b>");
            }
            else
            {
                $Config = json_decode(file_get_contents($File));

                if(json_last_error() == JSON_ERROR_NONE)
                {
                    if(empty($Config->sdb))
                    {
                        exit("<b>There's no valid \"sdb\" object in your file.</b>");
                    }
                }
                else
                {
                    exit("<b>There's something wrong with your config file!</b>");
                }
            }

            $this->Host = $Config->sdb->host;
            $this->Username = $Config->sdb->username;
            $this->Password = $Config->sdb->password;
            $this->Database = $Config->sdb->database;
        }

        public function Connection()
        {
            $Connection = new mysqli($this->Host, $this->Username, $this->Password, $this->Database);

            if($Connection->connect_error)
            {
                exit();
            }
            else
            {
                return $Connection;
            }
        }
    }

    trait DataTable
    {
        public $Count = 0;
        public $IsTableEmpty = false;
        public $Table = null;

        public function FillTable($Query, $FetchBy = "assoc")
        {
            $Connection = $this->Connection();
            
            $Result = $Connection->query($Query);

            if (!$Result) 
            {
                exit(self::SQLERROR);
            }
            else
            {
                if(!$Result->num_rows == 0)
                {
                    $this->Count = $Result->num_rows;
                    $this->IsEmpty = false;
                    $this->Table = array();

                    if($FetchBy == "assoc")
                    {
                        for ($i=0; $i < $Result->num_rows; $i++) 
                        {
                            array_push($this->Table, $Result->fetch_array(MYSQLI_ASSOC));
                        }
                    }
                    else
                    {
                        for ($i=0; $i < $Result->num_rows; $i++) 
                        {
                            array_push($this->Table, $Result->fetch_array(MYSQLI_NUM));
                        }
                    }
                }
                else
                {
                    echo "test";
                    $this->Count = $Result->num_rows;
                    $this->IsEmpty = true;
                    $this->Table = null;
                }

                $Connection->close();
            }
        }
    }

    trait Effect
    {
        public $LastId = null;

        public function Insert($Query)
        {
            $Connection = $this->Connection();

            $Result = $Connection->query($Query);

            if(!$Result) 
            {
                exit(self::SQLERROR);
            }
            else
            {
                $this->LastId = $Connection->insert_id;
                $Connection->close();
            }
        }

        public function Update($Query)
        {
            $Connection = $this->Connection();

            $Result = $Connection->query($Query);

            if(!$Result) 
            {
                exit(self::SQLERROR);
            }
            else
            {
                $Connection->close();
            }
        }
    }

    trait Check
    {
        public function CountTable(string $Table)
        {
            $Connection = $this->Connection();
            
            $Result = $Connection->query("Select Count(*) From $Table");
            
            if(!$Result) 
            {
                exit(self::SQLERROR);
            }
            else
            {
                $Count = $Result->fetch_array(MYSQLI_NUM);
                $Connection->close();

                return (int)$Count[0];
            }
        }

        public function IsAvaible($Query)
        {
            $Connection = $this->Connection();

            $Result = $Connection->query($Query);

            if(!$Result) 
            {
                exit(self::SQLERROR);
            }
            else
            {
                $Connection->close();

                return ($Result->num_rows == 0) ? true : false;
            }
        }
    }
?>