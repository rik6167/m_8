<?
class Pagination
{
	protected $_db;
	
	var $next       = " > ";
	var $back       = " < ";
	var $first      = " |< ";
	var $last       = " >| ";
    var $noResults  = "No results found.";
    var $dir        = 'ASC';
    var $results;
    var $viewAll    = ' [ view all ] ';
    
	public function __construct()
    {		
		//$this->_db = Zend_Registry::get( 'db' );
				//$this->_db = Zend::registry( 'db' );

    }
	
	function getPagination($query, $order = null, $start = 0, $record = null, $pages = 14, $filter = null, $viewAll = false )
	{
		//Get Rows.        
		$queryTotal = $query;
        
        //Get total number rows
        #echo $query;
		$resultTotal = $this->_db->query($queryTotal);
		$rowsTotal = $resultTotal->fetchAll();		
		$fieldsTotal = count($rowsTotal);
        $this->results = $fieldsTotal;
		unset($rowsTotal);
        
        if ( $order != null )
        {            
            if ($order == 'price' or $order == 'o.amount' or $order == 'id' or $order == 'active' )
            {                
                $query .= " ORDER BY " . $order . " " . $this->dir;         
            }
            else
            {
                // We removed this becuase we had an issue with a DISTINCT
                //$query .= " ORDER BY upper(" . $order . ") " . $this->dir;
                $query .= " ORDER BY " . $order . " " . $this->dir;
            }
        }
        if ( $record != null )
        {
            if ( ($_REQUEST['viewAll'] == 'true') and ($viewAll === true))
            {
                $query .= ' LIMIT ' . $this->results . ' OFFSET 0';
            }
            else
            {
                $query .= ' LIMIT ' . $record . ' OFFSET ' . $start;
            }
        }
        
		$result = $this->_db->query($query);
		$rows = $result->fetchAll();
		
		$return['result'] = $rows;		
        
		//Get Control.
		if ( $record != null and $fieldsTotal != 0 )
		{
			$currentMax = $start + $record;
			if ($currentMax > $fieldsTotal)
			{
				$currentMax = $fieldsTotal;
			}
						
			$currentPage = ($start / $record) + 1;
			$show = '<div id="paginatioAll" ><div id="paginationShow">Showing: ' . ($start + 1) . '-' . $currentMax . ' of ' . $fieldsTotal .'</div>';
			$i = 0;
			$numberPage = 1;
			$page2 = round($pages / 2);
					
			
			$backPage = $start - $record;
			if ($backPage >= 0)
			{
				$show .= " <div id='paginationFirst' style='display:inline;'> <a href='$_SERVER[REDIRECT_URL]?start=0&order=$order&dir=$this->dir&$filter'>" . $this->first . "</a></div> ";
				$show .= " <div id='paginationBack' style='display:inline;'><a href='$_SERVER[REDIRECT_URL]?start=$backPage&order=$order&dir=$this->dir&$filter'>" . $this->back . "</a></div> ";
			}
			else
			{
				$show .= "<div id='paginationFirst' style='display:inline;'>" . $this->first . "</div>";
				$show .= "<div id='paginationBack' style='display:inline;'>" . $this->back . "</div>";
			}
			
			while ($i < $fieldsTotal)
			{			
				if ($i > $fieldsTotal)
				{
					true;
				}
				else
				{					
					if ( ($start != $i) or ($_REQUEST['viewAll'] == 'true') )
					{
						//$show .= " <a href='$_SERVER[REDIRECT_URL]?start=$i'>$numberPage</a> ";
						$pagesLink[] = " <a href='$_SERVER[REDIRECT_URL]?start=$i&order=$order&dir=$this->dir&$filter'>$numberPage</a> ";
						$lastPage = " <a href='$_SERVER[REDIRECT_URL]?start=$i&order=$order&dir=$this->dir&$filter'>";
					}
					else
					{
						//$show .= $numberPage;
						$pagesLink[] = ' ' . $numberPage . ' ';
					}
				}
				$i = $record + $i;
				$numberPage = $numberPage + 1;
			}
			
			$lastRow = $pagesLink[count($pagesLink) - 1];
			
			$nextPage = $start + $record;
			
			$pagesLink1 = $pagesLink;
			$pagesLink2 = $pagesLink;
			
			$count = $currentPage - $page2;
			while ($count > 0)
			{
				unset($pagesLink1[$count - 1]);
				$count--;
			}
			
			//$pagesLink1[$currentPage - $page2] .= '...';
			
			$count = $currentPage + $page2;
			while ($count < $numberPage)
			{
				unset($pagesLink2[$count - 1]);
				$count++;
			}
			
			$pagesLink = array_intersect($pagesLink1, $pagesLink2);
			
			foreach ($pagesLink as $index => $value)
			{
				$newArray[] = $index;
			}
			
			//echo $newArray[0];
			if ( $newArray[0] != 0 )
			{
				$pagesLink[$newArray[0]] = '...' . $pagesLink[$newArray[0]];
			}
			                       
			if ( ($newArray[count($newArray) - 1] != (ceil($fieldsTotal / $record) - 1)) AND ( (ceil($fieldsTotal / $record) - 1) > 0) )
			{
				$pagesLink[$newArray2] = '...' . $pagesLink[$newArray2];
			}
			
			$show .= " <div id='paginationPages' style='display:inline;'> ";
			foreach ($pagesLink as $index => $value)
			{
				$show .= $value;
			}
			$show .= " </div> ";
			
			if ($nextPage < $fieldsTotal)
			{				
				$show .= " <div id='paginationNext' style='display:inline;'><a href='$_SERVER[REDIRECT_URL]?start=$nextPage&order=$order&dir=$this->dir&$filter'>" . $this->next . "</a></div>";
				$show .= " <div id='paginationLast' style='display:inline;'>" . $lastPage . $this->last . "</a></div>";
                if ($viewAll == true)
                {
                    $show .= " <div id='viewAll' style='display:inline;'> <a href='$_SERVER[REDIRECT_URL]?viewAll=true&$filter'>$this->viewAll</a> </div>";
                }
                $show .= '</div>';
			}
			else
			{
				$show .= " <div id='paginationNext' style='display:inline;'> " . $this->next . "</div>";
				$show .= " <div id='paginationLast' style='display:inline;'> " . $this->last . '</div>';
                if ($viewAll == true)
                {
                    $show .= " <div id='viewAll' style='display:inline;'> <a href='$_SERVER[REDIRECT_URL]?viewAll=true&$filter'>$this->viewAll</a> </div>";
                }
                $show .= '</div>';
			}
			
			//$show .= $lastRow;
		}
		
		if ($fieldsTotal == 0)
		{
			$show = "<div id='paginationNot' style='display:inline;'> $this->noResults </div> ";
		}
		
		//echo $shio
		
		$return['control'] = $show;
        
        if ($this->dir == 'ASC')
        {
            $this->dir = 'DESC';
        }
        else
        {
            $this->dir = 'ASC';
        }
		
		return $return;
	}
}	
?>