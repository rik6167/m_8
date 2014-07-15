<?php
/**
 * Action Helper for loading forms
 *
 * @uses Zend_Controller_Action_Helper_Abstract
 */
class App_Helper_Action_MyHelperAction extends Zend_Controller_Action_Helper_Abstract
{
	/**
	 * First entry is for January
	 */
	protected $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    /**
     * @var Zend_Loader_PluginLoader
     */
    public $pluginLoader;

    /**
     * Constructor: initialize plugin loader
     *
     * @return void
     */
    public function __construct()
    {
        $this->pluginLoader = new Zend_Loader_PluginLoader();
    }

    public function getDaysInMonth($month, $year)
    {
        if ($month < 1 || $month > 12)
        {
            return 0;
        }

        $d = $this->daysInMonth[$month - 1];

        if ($month == 2)
        {
            // Check for leap year
            // Forget the 4000 rule, I doubt I'll be around then...

            if ($year%4 == 0)
            {
                if ($year%100 == 0)
                {
                    if ($year%400 == 0)
                    {
                        $d = 29;
                    }
                }
                else
                {
                    $d = 29;
                }
            }
        }

        return $d;
    }   

    public function getArrSiNo()
    {
        return array ( 'S' => 'Si', 'N' => 'No' );
    }


    public function getArrTiposInstitucion()
    {
        return array ( TIPO_INSTITUCION_BIBLIOTECA => TIPO_INSTITUCION_BIBLIOTECA . ' - BIBLIOTECA'
            , TIPO_INSTITUCION_INSTITUCION => TIPO_INSTITUCION_INSTITUCION . ' - INSTITUCION' );
    }

    public function getArrTiposPersona()
    {
        return array ( TIPO_PERSONA_COORDINADOR => TIPO_PERSONA_COORDINADOR . ' - COORDINADOR'
            , TIPO_PERSONA_DOCENTE => TIPO_PERSONA_DOCENTE . ' - DOCENTE'
            , TIPO_PERSONA_RECTOR => TIPO_PERSONA_RECTOR . ' - RECTOR' );
    }

    public function getArrTiposSede()
    {
        return array ( TIPO_SEDE_PRINCIPAL => 'PRINCIPAL'
            , TIPO_SEDE_ALTERNA => 'ALTERNA' );
    }

    public function getArrTiposZona()
    {
        return array ('RURAL' => 'RURAL', 'URBANA' => 'URBANA' );
    }

    public function getArrModalidadSede()
    {
        return array (SEDE_CARACTER_ACADEMICO => SEDE_CARACTER_ACADEMICO, SEDE_CARACTER_TECNICO => SEDE_CARACTER_TECNICO );
    }

    public function getArrEstadosActGr()
    {
        return array (ESTADO_ACTGR_PENDIENTE => ESTADO_ACTGR_PENDIENTE, ESTADO_ACTGR_EJECUTADA => ESTADO_ACTGR_EJECUTADA, ESTADO_ACTGR_CANCELADA => ESTADO_ACTGR_CANCELADA );
    }

    public function getListaItemById($pId)
    {
        $mlistas = new Default_Model_ListaItems();
        $items = $mlistas->find($pId);
        foreach ($items as $item) {
                return $item;
            }
            return null;
     }

    public function getListaItemValorById($pId)
    {
        $item = self::getListaItemById($pId);
        return (isset($item)) ?$item->valor:'';
    }

    public function getSedeById($pId)
    {
        $mEntidad = new Default_Model_Sedes();
        $items = $mEntidad->find($pId);
        foreach ($items as $item) {
                return $item;
            }
            return null;
     }

    public function getSedeNombreById($pId)
    {
        $item = self::getSedeById($pId);
        return (isset($item)) ?$item->nombre:'';
    }

    public function getInstitucionById($pId)
    {
        $mEntidad = new Default_Model_Instituciones();
        $items = $mEntidad->find($pId);
        foreach ($items as $item) {
                return $item;
            }
            return null;
     }

    public function getInstitucionNombreById($pId)
    {
        $item = self::getInstitucionById($pId);
        return (isset($item)) ?$item->nombre:'';
    }

    public function getGrupoById($pId)
    {
        $mEntidad = new Default_Model_Grupos();
        $items = $mEntidad->find($pId);
        foreach ($items as $item) {
                return $item;

            }
            return null;
     }

    public function getGrupoNombreById($pId)
    {
        $item = self::getGrupoById($pId);
        if (isset($item)) {
            $grado = self::getListaItemValorById($item->grado);
            return $grado . ' ' . $item->codigo;
        } else {
            return '';
        }
    }
    
}


?>
