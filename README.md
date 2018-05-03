# jsnlib-joomla_pagination
幫助 Joomla! 結構化分頁輔助，操作起來更明確。

## 使用方法
````php
$pagination = new \Jsnlib\Joomla\Pagination($this);

$pagination
    
    // 每頁多少筆
    ->limit(10)
    
    // 每頁起始值
    ->offset(0)
    
    // 未分頁的總數量
    ->total(100)
    
    // 建立
    ->create(function ($joomla, $param)
    {
        // 返回分頁的數據列表
        return $joomla->model->getAll(new \Jsnlib\Ao
        ([
            'offset' => $param->offset,
            'limit' => $param->limit
        ]));
    });
````

- limit()：通常是固定的數量。
- offset()：因為換頁的關係，通常接收來自 $_GET 參數值。
- total()：從 DB 計算出的未分頁的數量。
- create()：透過 callable 取得依照 limit, offset 取得的實際列表。

其中 limit(), offset(), total() 可以直接賦予數量，也可以使用匿名函式後回傳，例如
````php
$pagination->limit(function ()
{
    // do something ......
    return 5;
})
````

## 使用範例
這裡示範在 controller 建立分頁，並傳送到 view.html.php，若要在 view.html.php 建立分頁也是沒有問題的。
administrator/components/com_todolist/controllers/todolist.php
````php
<?php 
class TodoListControllerTodoList extends JControllerLegacy
{
    public function index()
    {
        $pagination = new \Jsnlib\Joomla\Pagination($this);

        $view = $this->getView('TodoList', 'html');

        $view->setLayout('main');

        $view->pagination = $pagination
            
            // 每頁多少筆
            ->limit(10)
            
            // 每頁起始值
            ->offset($this->post->getInt('limitstart', 0))
            
            // 未分頁的總數量
            ->total(function ($joomla)
            {
                return $joomla->something_model->getNumAllFilter();
            })
            
            // 建立
            ->create(function ($joomla, $param)
            {
                // 返回分頁的數據列表
                return $joomla->something_model->getAllFilter(new \Jsnlib\Ao
                ([
                    'offset' => $param->offset,
                    'limit' => $param->limit
                ]));
            });
        
        $view->main();
    }
}
````

輸出列表
administrator/components/com_todolist/views/todolist/tmpl/main.php
````php
<?php foreach ($this->pagination->collection as $key => $something): ?>
    <?=$something->id?>
<?php endforeach; ?>
````

輸出分頁
````php
<?=$this->pagination->joomlaPagination->getListFooter(); ?>
````

