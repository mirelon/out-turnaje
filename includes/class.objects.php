<?PHP
    // Stick your DBOjbect subclasses in here (to help keep things tidy).

    class User extends DBObject
    {
        public function __construct($id = null)
        {
            parent::__construct('users', array('nid', 'username', 'password', 'level'), $id);
        }
    }
    
    class Hrac extends DBObject
    {
        public function __construct($id = null)
        {
            parent::__construct('hraci', array('meno', 'priezvisko', 'prezyvka', 'profil', 'externe', 'domaci_tim', 'poznamka', 'foto'), $id);
        }      
        
        public function link($text, $get_od = null, $get_do = null)
		    {			  
            $return = '<a href="hrac.php?id='.$this->id;
            if(!is_null($get_od)) $return .= '&amp;od='.$get_od;
            if(!is_null($get_do)) $return .= '&amp;do='.$get_do;
            $return .= '">'.$text.'</a>';
		        return $return; 			    
    		}
    		
    		public function link_zmen()
		    {			  
		        return '<a href="admin-hrac.php?akcia=zmen&amp;id='.$this->id.'">zmeň hráča</a>';			    
    		}
    		
    		public function link_zmaz()
		    {			  
		        return '<a onClick="return potvrd()" href="admin-hrac.php?akcia=zmaz&amp;id='.$this->id.'">zmaž hráča</a>';
    		}
    		
    		public function riadok_tabulky($get_od = null, $get_do = null)
		    {			  
		        $return = '<tr><td>';
		        $return .= $this->link($this->prezyvka, $get_od, $get_do);
		        $return .='</td><td>';
		        $return .= $this->meno;
		        $return .='</td><td>';
		        $return .= $this->priezvisko;
		        $return .='</td><td>';
		        $return .= $this->domaci_tim;
		        $return .='</td><td>';
		        $return .= $this->pocet_turnajov($get_od, $get_do);
            $return .='</td><td>';
            $return .= $this->priemerne_umiestnenie($get_od, $get_do).' z '.$this->priemerny_pocet_timov($get_od, $get_do);
            $return .='</td><td>';
            $return .= $this->pokorene_timy($get_od, $get_do);
            $return .='</td><td>';
		        $return .= $this->pocet_spiritov($get_od, $get_do);		        
            $return .= '</td></tr>';		    
            return $return; 			    
    		}
    		
    		public function riadok_tabulky_admin_zostava($id_turnaj)
		    {			  
		        $return = '<tr><td>';
		        $return .= $this->link($this->prezyvka);
		        $return .='</td><td>';
		        $return .= $this->meno;
		        $return .='</td><td>';
		        $return .= $this->priezvisko;
		        $return .='</td><td>';
		        $return .= $this->domaci_tim;		        		        
		        $return .='</td><td>';
		        $return .= '<a onClick="return potvrd()" href="admin-zostava.php?id='.$id_turnaj.'&amp;akcia=zmaz&amp;hrac='.$this->id.'">zmaž zo zostavy</a>';                  
            $return .= '</td></tr>';		    
            return $return; 			    
    		}
    		
    		public function turnaje($get_od = null, $get_do = null)
		    {			  
			      $db = Database::getDatabase();			       
            $query = "SELECT z.turnaj FROM zostavy AS z, turnaje AS t WHERE z.hrac = '{$this->id}' AND z.turnaj = t.id";
                        
            $query .= make_datum_od_query("AND t.datum_od", $get_od);
            $query .= make_datum_do_query("AND t.datum_od", $get_do);
                       
			      return $db->getValues($query);
    		}   

		    public function pocet_turnajov($get_od = null, $get_do = null)
		    {			  
			      $db = Database::getDatabase();
            $query = "SELECT * FROM zostavy AS z, turnaje AS t WHERE z.hrac = '{$this->id}' AND z.turnaj = t.id";
                        
            $query .= make_datum_od_query("AND t.datum_od", $get_od);
            $query .= make_datum_do_query("AND t.datum_od", $get_do);
              
			      return $db->numRows($query);
    		}
    		
    		public function pocet_spiritov($get_od = null, $get_do = null)
		    {			  
			      $db = Database::getDatabase();        
            $query = "SELECT t.id FROM zostavy AS z, turnaje AS t WHERE z.hrac = '{$this->id}' AND z.turnaj = t.id AND t.spirit>0";
            
            $query .= make_datum_od_query("AND t.datum_od", $get_od);
            $query .= make_datum_do_query("AND t.datum_od", $get_do);
            
			      return $db->numRows($query);
    		}
        
        public function umiestnenia($nenulovy_vysledok, $nenulovy_pocet, $get_od = null, $get_do = null)
		    {			  
			      $db = Database::getDatabase();
			      $query = "SELECT t.vysledok FROM turnaje AS t, zostavy AS z WHERE z.hrac = '{$this->id}' AND z.turnaj=t.id";
            if($nenulovy_vysledok) $query .= " AND t.vysledok>0";
            if($nenulovy_pocet) $query .= " AND t.pocet_timov>0"; 
            
            $query .= make_datum_od_query("AND t.datum_od", $get_od);
            $query .= make_datum_do_query("AND t.datum_od", $get_do);
            
			      return $db->getValues($query);
    		}
        
        public function pocty_timov($nenulovy_pocet, $nenulovy_vysledok, $get_od = null, $get_do = null)
		    {			  
			      $db = Database::getDatabase();
			      $query = "SELECT t.pocet_timov FROM turnaje AS t, zostavy AS z WHERE z.hrac = '{$this->id}' AND z.turnaj=t.id";
            if($nenulovy_vysledok) $query .= " AND t.vysledok>0";
            if($nenulovy_pocet) $query .= " AND t.pocet_timov>0";
            
            $query .= make_datum_od_query("AND t.datum_od", $get_od);
            $query .= make_datum_do_query("AND t.datum_od", $get_do);
             
			      return $db->getValues($query);
    		}        
    		
    		public function priemerne_umiestnenie($get_od = null, $get_do = null)
		    {
            $umiestnenia = $this->umiestnenia(true, true, $get_od, $get_do);
            if(count($umiestnenia) == 0) return 0;			  
			      else return round(array_sum($umiestnenia)/count($umiestnenia));
    		}
    		
    		public function priemerny_pocet_timov($get_od = null, $get_do = null)
		    {
            $pocty = $this->pocty_timov(true, true, $get_od, $get_do);
            if(count($pocty) == 0) return 0;			  
			      else return round(array_sum($pocty)/count($pocty));
    		}

                public function pokorene_timy($get_od = null, $get_do = null)
                    {
                $umiestnenia = $this->umiestnenia(true, true, $get_od, $get_do);
                $pocty = $this->pocty_timov(true, true, $get_od, $get_do);
                return array_sum($pocty) - array_sum($umiestnenia);
                }
        
        public function kolko_spolu_hrali($spoluhrac)
		    {			  
			      $db = Database::getDatabase();
			      return $db->numRows("SELECT t.id FROM turnaje AS t LEFT JOIN (zostavy AS z1, zostavy as z2) 
                                ON (t.id = z1.turnaj AND t.id = z2.turnaj) WHERE z1.hrac = '{$this->id}' AND z2.hrac = '{$spoluhrac}'");
    		}    		  
    		
    		public function najcastejsi_spoluhraci($kolko = 6)
		    {
		        //selectneme vsetkych spoluhracov okrem daneho
		        $spoluhraci = DBObject::glob('Hrac', "SELECT * FROM hraci WHERE id <> '{$this->id}'");
		        
		        //polia, v ktorych si budeme pametat id spoluhraca a pocet turnajov, na ktorom spolu hrali 
            $return = array();
            //naplnime nulami
            $return2 = array();
                        
            foreach($spoluhraci as $s) {
              $return[$s->id] = $this->kolko_spolu_hrali($s->id);
            }	
            
            //utriedime podla poctu odohratych turnajov
            arsort($return);
            $i = 0;
            
            //vyberieme len prvych "kolko"
            foreach($return as $k=>$v) {
              if($i < $kolko) {
                $return2[$k] = $v;
                $i++;
              }
            }
            		  
			      return $return2; 
    		}

    }
    
    class Zapas extends DBObject
    {           
        public function __construct($id = null)
        {
            parent::__construct('zapasy', array('turnaj', 'super', 'bodov_Out', 'bodov_super'), $id);
        }   
        
        public function link($text, $get_od = null, $get_do = null)
		    {			  
            $return = '<a href="zapas.php?id='.$this->id;
            if(!is_null($get_od)) $return .= '&amp;od='.$get_od;
            if(!is_null($get_do)) $return .= '&amp;do='.$get_do;
            $return .= '">'.$text.'</a>';
		        return $return; 			    
    		}
        
        public function link_zmen()
		    {			  
		        return '<a href="admin-zapas.php?akcia=zmen&amp;id='.$this->id.'">zmeň zápas</a>';			    
    		}
    		
    		public function link_zmaz()
		    {			  
		        return '<a onClick="return potvrd()" href="admin-zapas.php?akcia=zmaz&amp;id='.$this->id.'">zmaž zápas</a>';
    		} 
        
        public function riadok_tabulky($get_od = null, $get_do = null)
		    {			  
		        $return = '<tr><td>';
/*		        $return .= $this->link($this->zapas, $get_od, $get_do);
            $return .='</td><td>';*/
		        $return .= $this->turnaj;
		        $return .= '</td><td>';
		        $return .= $this->super;
		        $return .= '</td><td>';
		        $return .= $this->bodov_Out .':'. $this->bodov_super;
            $return .= '</td></tr>';		    
            return $return; 			    
    		}        
    }        
    
    class Turnaj extends DBObject
    {           
        public function __construct($id = null)
        {
            parent::__construct('turnaje', array('turnaj', 'kategoria', 'datum_od', 'datum_do', 'vysledok', 'pocet_timov', 'tim_Out', 'spirit', 'zostava', 'report', 'stat', 'mesto', 'datum_zapisu'), $id);
        }                                                                             
        
        public function link($text, $get_od = null, $get_do = null)
		    {			  
            $return = '<a href="turnaj.php?id='.$this->id;
            if(!is_null($get_od)) $return .= '&amp;od='.$get_od;
            if(!is_null($get_do)) $return .= '&amp;do='.$get_do;
            $return .= '">'.$text.'</a>';
		        return $return; 			    
    		}
    		
    		public function link_zmen()
		    {			  
		        return '<a href="admin-turnaj.php?akcia=zmen&amp;id='.$this->id.'">zmeň turnaj</a>';			    
    		}
        
        public function link_zmen_zostavu()
		    {			  
		        return '<a href="admin-zostava.php?id='.$this->id.'">zmeň zostavu</a>';			    
    		}
    		
    		public function link_zmaz()
		    {			  
		        return '<a onClick="return potvrd()" href="admin-turnaj.php?akcia=zmaz&amp;id='.$this->id.'">zmaž turnaj</a>';
    		}
        
        public function datum_od_format()
		    {			  
		        return ($this->datum_od != '0000-00-00') ? dater($this->datum_od, 'd.m.Y') : '';
    		}
        
        public function datum_do_format()
		    {			  
		        return ($this->datum_do != '0000-00-00') ? dater($this->datum_do, 'd.m.Y') : '';
    		}
        
        public function datum_format()
		    {			  
		        if(($this->datum_do == '0000-00-00') || ($this->datum_od == $this->datum_do)) 
              return dater($this->datum_od, 'd.m.Y');
            elseif(substr($this->datum_od, 0, 4) != substr($this->datum_do, 0, 4)) 
              return dater($this->datum_od, 'd.m.Y').' - '.dater($this->datum_do, 'd.m.Y');
            elseif(substr($this->datum_od, 5, 2) != substr($this->datum_do, 5, 2)) 
              return dater($this->datum_od, 'd.m.').' - '.dater($this->datum_do, 'd.m.Y');
            else
              return dater($this->datum_od, 'd.').' - '.dater($this->datum_do, 'd.m.Y');
    		}
    		
    		public function riadok_tabulky($get_od = null, $get_do = null)
		    {			  
		        $return = '<tr><td>';
		        $return .= $this->link($this->turnaj, $get_od, $get_do);
            $return .='</td><td>';
		        $return .= $this->nazov_kategorie();
		        $return .='</td><td>';
		        $return .= $this->mesto;
		        $return .='</td><td>';
		        $return .= $this->stat;
		        $return .='</td><td>';
		        $return .= $this->datum_format();
		        $return .='</td><td>';
            /*
		        $return .= $this->nazov_timu();
		        $return .='</td><td>';
            */
		        $return .= $this->vysledok.' z '.$this->pocet_timov;
		        $return .='</td><td>';
		        $return .= $this->spirit_slovne();
            $return .= '</td></tr>';		    
            return $return; 			    
    		}
    		
    		public function nazov_timu()
		    {			  
		        $db = Database::getDatabase();
			      return $db->getValue("SELECT ti.tim FROM timy AS ti, turnaje AS tu WHERE tu.id = '{$this->id}' AND tu.tim_Out = ti.id");			    
    		}
        
        public function spirit_slovne()
		    {			  
            return ($this->spirit != 0 ? 'áno' : 'nie');		        			    
    		}
        
        public function nazov_kategorie()
		    {			  
		        $db = Database::getDatabase();
			      return $db->getValue("SELECT ka.kategoria FROM kategorie AS ka, turnaje AS tu WHERE tu.id = '{$this->id}' AND tu.kategoria = ka.id");			    
    		}
    		
    		public function zostava()
		    {			  
			      $db = Database::getDatabase();
			      $query = "SELECT hrac FROM zostavy WHERE turnaj = '{$this->id}'";            
			      return $db->getValues($query);
    		}        
        
    }

