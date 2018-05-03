<?php 
namespace Jsnlib\Joomla;
/**
 * 結構化 Joomla! 的分頁設計
 */
class Pagination 
{
    protected $joomla;

    private $limit;
    private $offset;
    private $total;

    public function __construct($joomla)
    {
        $this->joomla =& $joomla;
        $this->limit = false;
        $this->offset = false;
        $this->total = false;
    }

    /**
     * 判斷 input 型態，取得的 input 值對應到屬性
     * 
     * @param string $property 內部屬性，通常是 offset|limit|total
     * @param mix    $input    int|callable    
     */
    protected function setCallableOrParam(string $property, $input): void
    {
        if (is_object($input))
        {
            if ($property == "total") 
            {
                $this->$property = $input($this->joomla);
            }
            else 
            {
                $this->$property = $input();
            }
        }
        else 
        {
            $this->$property = $input;
        }
    }

    /**
     * 上限值
     * @param  int|callable $param 上限值或程序返回的值
     */
    public function limit($param): object
    {
        $this->setCallableOrParam(__FUNCTION__, $param);

        return $this;
    }

    /**
     * 起始值
     * @param  int|callable $param 起始值或程序返回的值
     */
    public function offset($param): object
    {
        $this->setCallableOrParam(__FUNCTION__, $param);

        return $this;
    }

    public function total($param): object
    {
        $this->setCallableOrParam(__FUNCTION__, $param);

        return $this;
    }

    protected function check()
    {
        if ($this->limit === false) throw new \Exception(__CLASS__ . " 須要指定 limit()");
        elseif ($this->offset === false) throw new \Exception(__CLASS__ . " 須要指定 offset()");
        elseif ($this->total === false) throw new \Exception(__CLASS__ . " 須要指定 total()");
    }

    /**
     * 產生
     * @param  callable $callback [description]
     * @return \Jsnlib\Ao         [{Joomla 分頁物件}, {資料列表}]
     */
    public function create(callable $callback): \Jsnlib\Ao
    {
        $this->check();

        $param = new \Jsnlib\Ao(
        [
            'limit' => $this->get('limit'),
            'offset' => $this->get('offset'),
            'total' => $this->get('total')
        ]);

        $result = $callback($this->joomla, $param);

        $joomlaPagination = new \Joomla\CMS\Pagination\Pagination
        (
            $total = $this->get('total'),
            $offset = $this->get('offset'),
            $limit = $this->get('limit')
        );

        return new \Jsnlib\Ao(
        [
            'joomlaPagination' => $joomlaPagination, 
            'collection' => $result
        ]);
    }

    /**
     * 取得內部參數
     * @param  string $type 屬性名稱
     */
    public function get(string $type)
    {
        if (!isset($this->$type)) 
        {
            throw new \Exception("找不到 " . __CLASS__. " 屬性名稱 {$type}.");
        }
        
        return $this->$type;
    }

}
