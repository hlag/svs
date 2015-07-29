<?php
/*
 * Created on 31.05.2005
 * Projekt: Avaris-Godot-Dev
 *
 * by Matthias Günther
 *
 * Version: 2.8
 *
 * Changed 16.01.2007    MG
 * Bug fixed: "Zusammenklappen" des Moduletrees (IDs mit f�hrender "0" vergeben)
 *
 * Changed 31.01.2007     MG
 * - Einbau der Reiter-Struktur in 3 Ebenen (Beispiele Mainzer-Hausvertrieb, Tietmeyer)
 * - Steuerung der Reiterstruktur �ber die config.ini
 *
 *    Changed 01.02.2007     MG
 *    Neue Methode: unmarkTree. Diese ist n�tig, da ansonsten Probleme mit der Darstellung auftauchen
 *
 *    Changed 02.02.2007     MG
 *    - Neuer Parameter in der config.ini: showInactiveWithLink
 *    - Neuer Parameter in der config.ini: makeHomeDraggable
 *    - Neuer Parameter in der config.ini: homeWithChild
 *
 *    Changed 16.02.2007    MG
 *    Bug Fix: Bei Reiterdarstellung wurden teilweise Navigationen dargestellt.
 *
 *  Changed 28.03.2007 MG
 *  Bug Fix: Bei Reiterdarstellung wurde bei leerer Navigation ein <ul> ausgerendert. Solved
 *
 *     Changed 12.04.2007 MG
 *     Bug Fix: Module mit mod_ versehen, da ansonsten Seiteneffekte mit dem KontentTree auftauchen
 *
 *     Changed 31.05.2007 MG
 *     Bug Fix: Trenner wieder eingebaut. Elemente werden nach "</a>" getrennt
 *
 *    Changed 19.06.2007 MG
 *        Im Baum werden nun die ModuleNamen im class-Bereich klein geschrieben. (Neue Spec)
 *
 *    Changed 10.09.2007 MG
 *        Im BackendTree werden nur noch die Module ausgerendert, die auch ein Backend haben.
 *
 *    Changed 24.10.2007 MG
 *        showNaviInBackend implementiert
 *
 *  Changed 05.05.2008 MR
 *        render breadcrump: &raquo in den li gesetzt - da sonst w3c-validation problem auftritt.
 *
 *    Changed 06.08.2008 JA
 *        in renderBackendTree() wird jetzt auf aktuelles Modul geprueft und class="activeItem" gesetzt.
 *
 *  Changed 11.08.2008 JA
 *         in BackendTree gibt jetzt keine hrefs, sondern onclicks für die Änderungsüberwachung aus.
 * 
 *  Changed 19.08.2009 JA
 *         preview-Artikel wird nicht mehr im BackendTree dargestellt
 *
 * Changed 05.01.2010 MG
 *         Bug Fix: Wird bei Sprachen anders als deutsch immer auf die deutsche Hauptseite gelenkt
 * 
 * Changed 19.08.2010 MG
 *         Bug Fix: Auf AGDO geändert Einrückungen und  auf php5 gehoben
 *
 * Changed 29.06.2011 MG
 * 	New: span um active links
 *	Bug Fix: Falsches " am Ende des Links
 */

class TreeBuilder
{
    private $tree;
    private $DatabaseTree;
    private $navType;
    private $flavour;
    private $flavour2;
    private $reiter;
    private $flavourArray;
    private $flavourArray2;
    private $controller;
    private $lang;
    private $home;
    private $articleTable;
    private $descriptionTable;
    private $parentIDTable;
    private $dbconnector;
    private $user;
    private $settings;
    private $permission;
    private $moduleManager = null;

    public function __construct($Controller)
    {
        $this->controller = $Controller;
        $this->flavourArray=array();
        $this->dbconnector            = new DBConnector();
        $this->articleTable         = $this->dbconnector->getArticleTable();
        $this->descriptionTable     = $this->dbconnector->getDescriptionTable();
        $this->parentIDTable        = $this->dbconnector->getParentIDTable();
        $this->settings                = parse_ini_file(PATH."godot/.htconfig.ini", true);
    }
    
    public function setModuleManager($moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    public function setNavigation($NavType)
    {
        $this->navType=$NavType;
    }

    public function setLanguage($language)
    {
        $this->lang=$language;
    }

    public function setUser($user)
    {
        $this->user =$user;
    }

    public function getDatabaseTree()
    {
        return $this->DatabaseTree;
    }

    public function setReiter()
    {
        $this->reiter=true;
    }

    private function getReiter()
    {
        return $this->reiter;
    }

    private function getFlavour()
    {
        return $this->flavour;
    }

    private function setFlavour($flavour)
    {
        $this->flavour=$flavour;
    }

    private function setSecondFlavour($flavour)
    {
        $this->flavour2=$flavour;
    }

    private function getSecondFlavour($id)
    {
        if (isset($this->flavourArray2[$id]))
              return $this->flavourArray2[$id];
          return -1;
    }


    public function BuildTree($myid)
    {
        $this->unmarkTree($this->DatabaseTree);
        $this->tree=array();  //MG 16.02.2007    Tree mu� gel�scht werden, da ansonsten Seiteneffekte auftreten
        if ($this->navType=="navigation_ebene_3" )
        {
            if (isset($this->flavourArray2[$myid]))
            {
                $this->getTree($this->flavourArray2[$myid]);
            }
            else
            {
                if (isset($this->flavourArray[$myid]))
                {
                    $this->getSubTree($myid,$this->DatabaseTree);
                }
            }
        }
        else
        {
        if($this->getReiter() && isset($this->flavourArray[$myid]))
        {
            $this->getTree($this->flavourArray[$myid]);
            $this->setFlavour($this->flavourArray[$myid]);
        }
        else
            $this->getTree($myid);
        }
    }

    public function getTree($id)
    {
        $temp=$this->DatabaseTree;
        $this->getSubTree($id,$temp);
    }

    private function getSubTree($id, $leaf)
    {
        if (!empty($leaf))
        {
            for ($zaehler=0; $zaehler< count($leaf);$zaehler++ )
            {
                if ($leaf[$zaehler]['article_id']==$id)
                {
                    $this->tree= $leaf[$zaehler]['Child'];
                }
                else
                {
                    $temp=$this->getSubTree($id,$leaf[$zaehler]['Child']);
                }
            }
        }
        return "";
    }

    private function markTree($id)
    {
        $this->searchTree($id, $this->tree);
    }

    //Renders the Tree. Shows the Complete Leaf of the Branch
    public function renderTree($id)
    {

        $rechteverwaltung = ModuleManager::getInstance()->getModuleByName('Rechteverwaltung');
        $returnvalue="";
        if (empty($id))
            $id=4;
        $languageArray = Language::getInstance()->getLanguageArray();
        if ($this->lang==1)
            $language="";
        else
            $language = "";//$languageArray[$this->lang]['Name']."/";
        $class_id=$this->settings[$this->navType]['class_id'];
        $Bullit=$this->settings[$this->navType]['Bullit'];
        $BullitEnd=$this->settings[$this->navType]['BullitEnd'];
        $trenner=$this->settings[$this->navType]['trenner'];

        $this->markTree($id);
        $temp=$this->tree;
        if (!empty($temp))
            $returnvalue ="<ul id=\"".$this->settings[$this->navType]['class_id']."\">"."";
        for ($zaehler=0; $zaehler< count($temp);$zaehler++ )
        {
            if ($zaehler < count ($temp)-1)        //MG 31.05.2007
                $tempTrenner= $trenner;
            else
            {
                $tempTrenner="";
            }
            $show = true;
            if ((!empty($rechteverwaltung) && ($rechteverwaltung->hasPermission($temp[$zaehler]['article_id']))))
                    $show = false;
            //print_r ($rechteverwaltung);
            if ($temp[$zaehler]['article_url']!='' && $show)
            {
                $returnvalue.='<li class="'.$temp[$zaehler]['farbe'].'">';
                $inaktive=false;
                if (isset($this->flavourArray[$id]) && $temp[$zaehler]['article_id']==$this->flavourArray[$id])
                    $inaktive=true;
                if (isset($this->flavourArray2[$id])&& $temp[$zaehler]['article_id']==$this->flavourArray2[$id])
                    $inaktive=true;
                if (isset($this->flavourArray2[$id]) && isset($this->flavourArray2[$this->flavourArray2[$id]])&& $temp[$zaehler]['article_id']==$this->flavourArray2[$this->flavourArray2[$id]])
                    $inaktive=true;
                
                if (($id==$temp[$zaehler]['article_id'] ) || $inaktive )
                {

                    if ($temp[$zaehler]['article_type']==4 && !empty($temp[$zaehler]['alias_external_link']))
                    {
                        
                        $returnvalue.="<a href=\"".$temp[$zaehler]['alias_external_link']."\" target=\"_blank\">".$temp[$zaehler]['article_linktext']."</a>";
                    }
                    else
                    {
                        $parameter = "";
                        if (isset($temp[$zaehler]['link_parameter']))
                            $parameter="?".$temp[$zaehler]['link_parameter'];
                        //echo $temp[$zaehler]['link_parameter'];
                        if ($this->settings['Menuestruktur']['showInactiveWithLink'])        //MG 02.02.2007
                        {
                            if (isset($temp[$zaehler]['link_parameter']))
                            {
                                if ($temp[$zaehler]['module_mark'])
                                    $returnvalue.='<span class="inactive">'.$temp[$zaehler]['article_linktext']."</span>".$tempTrenner; //MG 31.05.200}
                                else
                                    $returnvalue.="<a href=\"".$language.($temp[$zaehler]['article_url']=="/"?"/\"":$temp[$zaehler]['article_url']
                        .".html".$parameter."\"").">".$Bullit.$temp[$zaehler]['article_linktext'].$BullitEnd."</a>".$tempTrenner;             //MG 31.05.2007
                            }
                            else
                                $returnvalue.="<span class=\"inactive\"><a href=\"".$language.($temp[$zaehler]['article_url']=="/"?"/\" ":$temp[$zaehler]['article_url'].".html".$parameter."\" ").">".$Bullit.$temp[$zaehler]['article_linktext'].$BullitEnd."</a></span>".$tempTrenner;    //MG 31.05.2007
                        }
                        else
                        {
                            $returnvalue.='<span class="inactive">'.$temp[$zaehler]['article_linktext']."</span>".$tempTrenner; //MG 31.05.2007
                        }
                    }
                }
                else
                {
                    if ($temp[$zaehler]['article_type']==4 && !empty($temp[$zaehler]['alias_external_link']))
                    {
                        $returnvalue.="<a href=\"".$temp[$zaehler]['alias_external_link']."\" target=\"_blank\">".$temp[$zaehler]['article_linktext']."</a>";
                    }
                    else
                    {
                        if ($this->lang!=1 && $temp[$zaehler]['article_id']==4) // Wird bei Sprachen anders als deutsch immer auf die deutsche Hauptseite gelenkt. 05.01.2010
                            $language="/".$languageArray[$this->lang]['Long'];
                        else
                            $language="";
                        $returnvalue.="<a href=\"".$language.($temp[$zaehler]['article_url']=="/"?"/\"":$temp[$zaehler]['article_url']
                        .".html\"").">".$Bullit.$temp[$zaehler]['article_linktext'].$BullitEnd."</a>".$tempTrenner;             //MG 31.05.2007
                    }
                }
                if ($temp[$zaehler]['mark'] &&  $this->settings[$this->navType]['showSubNav'] && isset($temp[$zaehler]['show_navi']) && $temp[$zaehler]['show_navi'] )
                         $returnvalue.=$this->showBreadCrumb($temp[$zaehler]['Child'],$id);
                $returnvalue.="</li>"."";
            }
        }
        if (!empty($temp))
            $returnvalue.="</ul>"."";
        //echo $returnvalue;
        return $returnvalue;
    }

    public function setPermission($permission)
    {
        $this->permission = $permission;
    }

    private function unmarkTree(&$tree)
    {
        if (empty($tree))
            return "";
        for($counter=0;$counter<count($tree); $counter++)
        {
            $tree[$counter]['mark']=false;
            if (isset($tree[$counter]['Child']))
                $this->unmarkTree($tree[$counter]['Child']);
        }
    }

    private function searchTree($id, &$leaf)
    {
        if (empty($leaf))
        {
            return false;
        }
        else
        {
            for ($zaehler=0; $zaehler< count($leaf);$zaehler++ )
            {
                if ($leaf[$zaehler]['article_id']==$id)
                {
                    for ($zaehler2=0; $zaehler2< count($leaf);$zaehler2++ )
                    {
                        $leaf[$zaehler2]['mark']=true;
                    }
                    if (!empty($leaf[$zaehler]['Child']))
                        $this->markleaf($leaf[$zaehler]['Child']);
                    return true;
                }
                else
                {
                    $temp=$this->searchTree($id,$leaf[$zaehler]['Child']);
                    if (!($leaf[$zaehler]['mark']))
                        $leaf[$zaehler]['mark']=$temp;
                    if ($temp)
                    {
                        for ($zaehler2=0; $zaehler2< count($leaf);$zaehler2++)
                            $leaf[$zaehler2]['mark']=true;
                        return $temp;
                    }
                }
            }
        }
    }

    private function markleaf(&$leaf)
    {
        for ($zaehler=0; $zaehler< count($leaf);$zaehler++ )
        {
            $leaf[$zaehler]['mark']=true;
        }
    }

    private function showBreadCrumb($leaf,$id)
    {
        $returnvalue=NULL;
        $languageArray = Language::getInstance()->getLanguageArray();
        if ($this->lang==1)
            $language="";
        else
            $language = "";//$languageArray[$this->lang]['Name']."/";
        if (empty($leaf))
        {
            return "";
        }
        else
        {
            $ulshow=false;
            $returnvalue2=NULL;
            for ($zaehler=0; $zaehler< count($leaf);$zaehler++ )
            {
                //echo $leaf[$zaehler]['article_id'].$leaf[$zaehler]['article_linktext'];
                if ($leaf[$zaehler]['mark']==true && $leaf[$zaehler]['article_url']!='')
                {
                    $ulshow=true;
                    $returnvalue2.="<li>"."";
                    $inaktive = false;
                    if (isset($this->flavourArray[$id]) && $leaf[$zaehler]['article_id']==$this->flavourArray[$id])
                        $inaktive=true;
                    if (isset($this->flavourArray2[$id])&& $leaf[$zaehler]['article_id']==$this->flavourArray2[$id])
                        $inaktive=true;
                   // if ($leaf[$zaehler]['article_id']==1000023)
                   // echo $leaf[$zaehler]['article_id'];
                    
                    if ($leaf[$zaehler]['article_type']==4 && !empty($leaf[$zaehler]['alias_external_link']))
                    {
                        $returnvalue2.="<a href=\"".$leaf[$zaehler]['alias_external_link']."\" target=\"_blank\">".$leaf[$zaehler]['article_linktext']."</a>";
                    }
                    else
                    {
                        if ($id==$leaf[$zaehler]['article_id'] || $inaktive)
                        {
                            $parameter = "";
                            if (isset($leaf[$zaehler]['link_parameter']))
                            {
                                if (($leaf[$zaehler]['mark'] && !isset($leaf[$zaehler]['module_mark'])) || (isset($leaf[$zaehler]['module_mark'])&& $leaf[$zaehler]['module_mark']))
                                {
                                    if ($this->settings['Menuestruktur']['showInactiveWithLink'])
                                        $returnvalue2.='<span class="inactive"><a  href="'.$language.($leaf[$zaehler]['article_url']=="/"?"/\"":$leaf[$zaehler]['article_url'].".html".$parameter."\"").'>'.$leaf[$zaehler]['article_linktext']."</a></span>";
                                    else
                                        $returnvalue2.='<span class="inactive">'.$leaf[$zaehler]['article_linktext']."</span>";
                                    
                                }
                                else
                                {
                                    $parameter="?".$leaf[$zaehler]['link_parameter'];
                                    $returnvalue2.="<a href=\"".$language.($leaf[$zaehler]['article_url']=="/"?"/\"":$leaf[$zaehler]['article_url'].".html".$parameter."\"").">".$leaf[$zaehler]['article_linktext']."</a>";      
                                }
                            }
                            else
                            {
                                if ($this->settings['Menuestruktur']['showInactiveWithLink'])
                                    $returnvalue2.='<span class="inactive"><a href="'.$language.($leaf[$zaehler]['article_url']=="/"?"/\"":$leaf[$zaehler]['article_url'].".html".$parameter."\"").'>'.$leaf[$zaehler]['article_linktext']."</a></span>";
                                else
                                    $returnvalue2.='<span class="inactive">'.$leaf[$zaehler]['article_linktext']."</span>";
                            }
                        }
                        else
                        {
                            $parameter = "";
                            if (isset($temp[$zaehler]['link_parameter']))
                                $parameter="?".$temp[$zaehler]['link_parameter'];
                            $returnvalue2.="<a href=\"".$language.($leaf[$zaehler]['article_url']=="/"?"/\"":$leaf[$zaehler]['article_url'].".html\"").">".$leaf[$zaehler]['article_linktext']."</a>";
                        }
                    }
                    if (isset($leaf[$zaehler]['show_navi']) && $leaf[$zaehler]['show_navi'])
                        $returnvalue2.=$this->showBreadCrumb($leaf[$zaehler]['Child'], $id);
                    $returnvalue2.="</li>"."";
                }
            }
            if ($ulshow)
            {
                $returnvalue.="<ul>";
                $returnvalue.=$returnvalue2;
                $returnvalue.="</ul>"."";
            }
            return $returnvalue;
        }
    }

    public function renderCompleteTree()
    {
        $temp=$this->DatabaseTree;
        return $this->buildRenderTree($temp);
    }

    public function buildRenderTree($leaf)
    {
        $returnvalue=NULL;
        if (empty($leaf))
        {
            return "";
        }
        else
        {
            $returnvalue.="<ul class='sitemap'>"."\n";
            for ($zaehler=0; $zaehler< count($leaf);$zaehler++ )
            {
                if ($leaf[$zaehler]['article_id']!=6 && $leaf[$zaehler]['article_id']!=7 && $leaf[$zaehler]['article_id']!=4 && $leaf[$zaehler]['article_linktext']!="Suchergebnisse")
                {
                    $returnvalue.="<li>"."\n";
                    if ($leaf[$zaehler]['article_id']!="1" && $leaf[$zaehler]['article_id']!="5"  && $leaf[$zaehler]['article_id']!="3"  && $leaf[$zaehler]['article_id']!="2")
                        $returnvalue.="\t <a href=\"".($leaf[$zaehler]['article_url']=="/"?"/\"":$leaf[$zaehler]['article_url']).".html\"".">".$leaf[$zaehler]['article_linktext']."</a>\n";
                    else
                        $returnvalue.="\t ".$leaf[$zaehler]['article_linktext']."";
                    $returnvalue.=$this->buildRenderTree($leaf[$zaehler]['Child']);
                    $returnvalue.="</li>"."\n";
                }
            }
            $returnvalue.="</ul>"."\n";
            return $returnvalue;
        }
    }

    public function renderBackendTree($modulManager,$id,$moduleName) // JA 06.08.2008, 11.08.2008
    {
        $temp=$this->getCompleteTreeFromDatabase(0,0);
        $ebene=0;
        $backendTree="\n".'<ul id="naviTree2" class="naviTree">'."\n";
        $backendTree.=$this->buildBackendTree($temp,$ebene,$id);
        $modulenames = $modulManager->getModuleNameListBackend();
        $counter=2;
        $backendTree.='<li class="ModulManager" id="88880" noChildren="true" noDrag="true" noSiblings="true"><a>Funktionen</a><ul>';
        foreach ($modulenames as $names)
        {
            if ($modulManager->getModuleByName($names)->showModuleInBackend())
            {
                $backendTree.='<li noChildren="true" id="888888'.$counter.'" noDrag="true" noSiblings="true" class="'.strtolower($names).'"><a '.($moduleName == $names ? 'class="activeItem" ' : '').'onclick="tools.changePage(\'index.php?Module='.$names.'\');return false;" href="#">'.$modulManager->getModuleTreeName($names).'</a>';
                $backendTree.=$modulManager->getModuleByName($names)->getTree();
                $backendTree.='</li>'."\n";
                $counter++;
            }
        }
        $backendTree.="</ul></li></ul>";
        return $backendTree;
    }

    /*
     * changed 01.12.2008 JA added article state info to class names
     */
    public function buildBackendTree($leaf,$ebene,$id) // JA 11.08.2008
    {
        $returnvalue=NULL;
        if (empty($leaf))
        {
            return "";
        }
        else
        {
            if ($ebene!=0)
            $returnvalue.="\n"."<ul>"."\n";
            for ($zaehler=0; $zaehler< count($leaf);$zaehler++ )
            {
                $isClickable = true;
                $class="";
                $style="";
                if ($leaf[$zaehler]['article_id']<20 )
                {
                    // check for viewable/published state. States for id==4 will override this later.
                    $class = ' class="ordner" noDrag="true" noSiblings="true"';
                    
                    if($leaf[$zaehler]['published'] !== '1') {
                        $class = 'class="ordner nonpublished" noDrag="true" noSiblings="true"';
                    }
                    elseif($leaf[$zaehler]['viewable'] !== '1') {                    
                        $class = 'class="ordner nonviewable" noDrag="true" noSiblings="true"';
                    }
                }
                if ($leaf[$zaehler]['article_id']==4 )
                {
                    if ($this->settings['Menuestruktur']['homeWithChild'])
                        $class = 'class="home"';
                    else
                        $class = 'noChildren="true" class="home"';
                    if (!$this->settings['Menuestruktur']['makeHomeDraggable'])
                        $class .=' noDrag="true"';
                }
                if ($leaf[$zaehler]['article_type']==4)
                    $class.=' class="alias" ';
                if ($leaf[$zaehler]['article_id']==7 ) {
                    $class = 'noDrag="true" noChildren="true" class="entwuerfe" noSiblings="true"'; // <-
                    $isClickable = false;
                }
                if ($leaf[$zaehler]['article_id']==6 ) {
                    $class = 'noDrag="true" noChildren="true" class="textbausteine" noSiblings="true"'; // <-
                    $isClickable = false;
                }
                if ($leaf[$zaehler]['article_id']==1 ) {
                    $class = 'noDrag="true" noChildren="true" class="root" noSiblings="true"'; // <-
                    $isClickable = false;
                }
                if ($leaf[$zaehler]['article_id']==8 )
                    $class = 'noDrag="true" noSiblings="true" class="papierkorb"';
                    
                if ($leaf[$zaehler]['article_id']==$id )
                    $style = 'class="activeItem"';
                
                // check for published/viewable state
                if($leaf[$zaehler]['article_id'] >=20 || $leaf[$zaehler]['article_id'] == 4)
                {
                    if($leaf[$zaehler]['published'] !== '1') {
                        $class = $leaf[$zaehler]['article_type'] == 4 ? 'class="alias nonpublished"' : 'class="article nonpublished"';
                    }
                    elseif($leaf[$zaehler]['viewable'] !== '1') {                    
                        $class = $leaf[$zaehler]['article_type'] == 4 ? 'class="alias nonviewable"' : 'class="article nonviewable"';
                    }    
                }
                if ($this->showElementInBackend($leaf[$zaehler]['article_id']))
                {
                     $returnvalue.="\t".'<li id="'.$leaf[$zaehler]['article_id'].'" '.$class.'>'; // with non-numeric id, tree won't display last state
                     /*
                    if ($leaf[$zaehler]['article_id']==1)
                        $returnvalue.="<a href=\"index.php\" ".$style.">".$leaf[$zaehler]['article_linktext']."</a>";
                    else
                        $returnvalue.="<a href=\"index.php?ID=".$leaf[$zaehler]['article_id']."\" ".$style.">".$leaf[$zaehler]['article_linktext']."</a>";
                    */

                    if ($leaf[$zaehler]['article_id']==1)
                        $returnvalue.="<a onclick=\"tools.changePage('index.php');\" ".$style.">".$leaf[$zaehler]['article_linktext']."</a>";
                    else
                        $returnvalue.="<a ".($isClickable ? ("onclick=\"tools.changePage('index.php?ID=".$leaf[$zaehler]['article_id']."');return false;\" href=\"#\" ") : "" ).$style.">".$leaf[$zaehler]['article_linktext']."</a>";
                    $neuEbene= $ebene+1;
                    $returnvalue.=$this->buildBackendTree($leaf[$zaehler]['Child'],$neuEbene,$id);
                    $returnvalue.="</li>"."\n";
                }
            }
            if ($ebene!=0)
                $returnvalue.="</ul>"."\n";
            return $returnvalue;
        }
    }

    private function showElementInBackend($id)
    {
        if($id == $this->settings['preview']['id'])
            return false;
        if ($id>12)
            return true;
        switch ($id)
        {
            case 2:
                return $this->settings['Header']['showNaviInBackend'];
            case 3:
                return $this->settings['Vertical']['showNaviInBackend'];
            case 5:
                return $this->settings['Main']['showNaviInBackend'];
            case 9:
                return $this->settings['Support']['showNaviInBackend'];
            case 10:
                return $this->settings['Footer']['showNaviInBackend'];
            case 12:
                return $this->settings['Landingpages']['showNaviInBackend'];
            default:
                return true;
        }
    }

     //Renders the Breadcrumb
    public function renderBreadCrumb($id)
    {
        //print_r($this->DatabaseTree);
        if (empty($this->DatabaseTree[0]['Child']))
            return "";
        $this->tree=$this->DatabaseTree[0]['Child'];
        $this->markSingleTree($id,$this->tree);
        return $this->buildBreadCrumb($this->tree,0,$id);
    }

    //Marks only the Tree to the Element id nothing. Can not be used for Sitenavigation. Only with breadcrump.
    private function markSingleTree($id, &$leaf)
    {
        if (empty($leaf))
        {
            return false;
        }
        else
        {
            for ($zaehler=0; $zaehler< count($leaf);$zaehler++ )
            {
                //echo $zaehler."<br>";
                if ($leaf[$zaehler]['article_id']==$id)
                {
                    $leaf[$zaehler]['mark']=true;
                    return true;
                }
                else
                {
                    $temp=$this->markSingleTree($id,$leaf[$zaehler]['Child']);
                    if (!($leaf[$zaehler]['mark']))
                        $leaf[$zaehler]['mark']=$temp;
                    if ($temp)
                    {
                    //for ($zaehler2=0; $zaehler2< count($leaf);$zaehler2++ )
                    {
                    //echo "mark".$leaf[$zaehler]['article_linktext']."<br>";
                        $leaf[$zaehler]['mark']=true;
                    }
                        return $temp;
                    }
                }
            }
        }
    }

    //Recursive Method that builds the breadcrump
    public function buildBreadCrumb($leaf, $ebene,$id)
    {
        $returnvalue=NULL;
        if (empty($leaf))
        {
            return "";
        }
        else
        {
            if ($ebene==0)
            $returnvalue.="<ul>";
            for ($zaehler=0; $zaehler< count($leaf);$zaehler++ )
            {
                if ($leaf[$zaehler]['mark']=="true")
                {
                    if ($ebene>0)
                    {
                        $returnvalue.="<li> &raquo;"."";
                        if ($leaf[$zaehler]['article_id']==$id)
                            $returnvalue.='<span class="inactive">'.$leaf[$zaehler]['article_linktext']."</span>";
                        else
                            $returnvalue.="<a href=\"".($leaf[$zaehler]['article_url']=="/"?"/\"":$leaf[$zaehler]['article_url'].".html\"").">".$leaf[$zaehler]['article_linktext']."</a>";
                        $returnvalue.="</li>"."";
                    }
                    else
                    {
                        $returnvalue.="<li>"."";
                        if ($leaf[$zaehler]['article_id']==$id)
                            $returnvalue.='<span class="inactive">'.$this->home['article_linktext']."</span>";
                        else
                            $returnvalue.="<a href=\"".($this->home['article_url']=="/"?"/\"":$this->home['article_url'].".html\"").">".$this->home['article_linktext']."</a>";
                        $returnvalue.="</li>"."";
                    }
                    $returnvalue.=$this->buildBreadCrumb($leaf[$zaehler]['Child'],($ebene+1),$id);
                }
            }
            if ($ebene==0)
                $returnvalue.="</ul>"."\n";
            return $returnvalue;
        }
    }

    //Nomen est omen
    public function connect($DBVerbindung)
    {
        //$this->dbverbindung=$DBVerbindung;
        $this->DatabaseTree=$this->getTreeFromDatabase(0,0);
        $this->setFlavour(0);
    }

    public function connectNoTree($DBVerbindung)
    {
        //$this->dbverbindung=$DBVerbindung;
    }

    public function getPermission($id, $user)
    {
        $SQLQuery = 'SELECT min(permission) FROM '.AGDO::getInstance()->getDBConnector()->getPrefix().'`user` ' .
                    'join '.AGDO::getInstance()->getDBConnector()->getPrefix().'user_to_role ON '.AGDO::getInstance()->getDBConnector()->getPrefix().'user.UserId = '.AGDO::getInstance()->getDBConnector()->getPrefix().'user_to_role.user_id ' .
                    'join '.AGDO::getInstance()->getDBConnector()->getPrefix().'permission USING (role_id) ' .
                    'WHERE UserId = '.$user['UserId'].' and '.AGDO::getInstance()->getDBConnector()->getPrefix().'permission.article_id = '.$id;
        $temp=AGDO::getInstance()->GetAll($SQLQuery);
        if (!empty($temp[0]['min(permission)']))
            return $temp[0]['min(permission)'];
    }
    
    /**
     *
     *    Recursive method that gets the whole tree from Database
     *
     */
     //TODO: Calculate the Tree in the Backend and store it in DB.
     // GET THE TREE FROM FRONTEND
    public function getTreeFromDatabase($id,$ebene)
    {
        $aktDate=date("Y")."-".date("n")."-".date("j");
        $SQLQuery="Select ".$this->articleTable.".article_id, ".$this->articleTable.".article_type, "
                    .$this->descriptionTable.".alias_external_link, "
                    .$this->descriptionTable.".article_linktext, "
                    .$this->descriptionTable.".Module, "
                    .$this->descriptionTable.".module_has_tree, "
                    .$this->descriptionTable.".article_url, "
                    .$this->descriptionTable.".article_content, "
                    .$this->descriptionTable.".article_as_link, "
                    .$this->descriptionTable.".module_parameter, "
                    .$this->descriptionTable.".show_navi, "
                    .$this->parentIDTable.".parent_id, ".$this->descriptionTable.".farbe " .
                    "FROM ".$this->articleTable." " .
                    "join ".$this->descriptionTable." USING (article_id) ".
                    "join ".$this->parentIDTable." USING (article_id) ";
        $SQLQuery.="where ".$this->parentIDTable.".parent_id='".$id."' " .
                    "and ".$this->articleTable.".published='1' and ".$this->articleTable.".viewable='1' and language_id='".$this->lang."' ".
                    " ORDER by sort_order";
        $result =  AGDO::getInstance()->GetAll($SQLQuery);
        if(!empty($result))
        {
            for ($zaehler=0; $zaehler< count($result);$zaehler++ )
            {
                if ($result[$zaehler]['article_id']=="4")
                    $this->home=$result[$zaehler];
                if ($ebene==2)
                {
                    $this->setFlavour($result[$zaehler]['article_id']);
                }
                if ($ebene==4)
                {
                    $this->setSecondFlavour($result[$zaehler]['parent_id']);
                }
                if ($result[$zaehler]['article_type']=="4" && $result[$zaehler]['article_as_link'])
                    $result[$zaehler]['article_url']=$this->getArticleURL($result[$zaehler]['article_content']);
                $result[$zaehler]['mark']=false;
                if (!empty($this->user))
                    $result[$zaehler]['permission']=$this->getPermission($result[$zaehler]['article_id'], $this->user);
                else
                {
                    $result[$zaehler]['permission']="2";  ///
                }
                $moduleTree=array();
                if (!empty($result[$zaehler]['Module'])&& $result[$zaehler]['show_navi'])
                {
                    $modul = ModuleManager::getInstance()->getModuleByName($result[$zaehler]['Module']);
                    if (!empty($modul))
                    {
                        $modul->setConnection(AGDO::getInstance());
                        if ($modul->hasFrontendNavigation())
                            $moduleTree = $modul->getFrontendNavigationData(Request::getInstance()->getRequests(),$result[$zaehler]);
                    }
                }
                //if ($result[$zaehler]['show_navi'])
                    $result[$zaehler]['Child']=$this->getTreeFromDatabase($result[$zaehler]['article_id'],($ebene+1));
                /*else
                    $result[$zaehler]['Child']=array();*/
                if (!empty($moduleTree))
                    $result[$zaehler]['Child']=array_merge($result[$zaehler]['Child'], $moduleTree);
                if ($ebene>2)
                {
                    $this->flavourArray[$result[$zaehler]['article_id']]=$this->getFlavour();
                    $result[$zaehler]['flavour']=$this->getFlavour();
                }
                if ($ebene>3)
                {
                    $this->flavourArray2[$result[$zaehler]['article_id']]=$result[$zaehler]['parent_id'];
                    $result[$zaehler]['flavour2']=$result[$zaehler]['parent_id'];
                    $this->setSecondFlavour($result[$zaehler]['parent_id']);
                    $result[$zaehler]['self']=$result[$zaehler]['article_id'];
                }
            }
        }
        return $result;
    }

    private function getArticleURL($article_url)
    {
        $SQLQuery = "SELECT article_url FROM ".$this->descriptionTable." WHERE article_id = '".$article_url."' and language_id = ".$this->lang;
        $result=AGDO::getInstance()->GetAll($SQLQuery);
        return $result[0]['article_url'];
    }

    /*
     * changed 01.12.2008 JA: also get viewable and published info
     */
    public function getCompleteTreeFromDatabase($id,$ebene)
    {
        $aktDate=date("Y")."-".date("n")."-".date("j");
        $SQLQuery="Select ".$this->articleTable.".article_id, ".$this->articleTable.".published, ".$this->articleTable.".viewable, ".$this->descriptionTable.".article_linktext, ".$this->articleTable.".article_type, ".$this->descriptionTable.".article_url, ".$this->parentIDTable.".parent_id , ".$this->descriptionTable.".farbe ".
                    "FROM ".$this->articleTable." join ".$this->descriptionTable." ON ".$this->articleTable.".article_id = ".$this->descriptionTable.".article_id " .
                    "join ".$this->parentIDTable." ON ".$this->descriptionTable.".article_id = ".$this->parentIDTable.".article_id " .
                    "where ".$this->parentIDTable.".parent_id='".$id."' " .
                    "and language_id='".$this->lang."' ".
                    "ORDER by sort_order";
        $result =  AGDO::getInstance()->GetAll($SQLQuery);
        if(!empty($result))
        {
            for ($zaehler=0; $zaehler< count($result);$zaehler++ )
            {
                if ($result[$zaehler]['article_id']=="4")
                    $this->home=$result[$zaehler];
                if ($ebene==2)
                {
                    $this->setFlavour($result[$zaehler]['article_id']);
                }
                if ($ebene==4)
                {
                    $this->setSecondFlavour($result[$zaehler]['parent_id']);
                }
                $result[$zaehler]['mark']=false;
                $result[$zaehler]['Child']=$this->getCompleteTreeFromDatabase($result[$zaehler]['article_id'],($ebene+1));
                if ($ebene>2)
                {
                    $this->flavourArray[$result[$zaehler]['article_id']]=$this->getFlavour();
                    $result[$zaehler]['flavour']=$this->getFlavour();
                }
                if ($ebene>3)
                {
                    $this->flavourArray2[$result[$zaehler]['article_id']]=$result[$zaehler]['parent_id'];
                    $result[$zaehler]['flavour2']=$result[$zaehler]['parent_id'];
                    $this->setSecondFlavour($result[$zaehler]['parent_id']);
                    $result[$zaehler]['self']=$result[$zaehler]['article_id'];
                }
            }
        }
        return $result;
    }
}
?>