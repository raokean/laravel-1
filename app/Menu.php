<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Menu as LavMenu; //lavary/laravel-menu

class Menu extends Model
{
    protected $fillable = ['title', 'label', 'parent_id', 'public_date', 'un_public_date'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    /*
 * Формирование пунктов меню используя расширение
 * https://github.com/lavary/laravel-menu#installation
 */
    public static function buildMenu ($arrMenu){
        $mBuilder = LavMenu::make('DefaultNav', function($m) use ($arrMenu){
            foreach($arrMenu as $item){
                /*
                 * Для родительского пункта меню формируем элемент меню в корне
                 * и с помощью метода id присваиваем каждому пункту идентификатор
                 */
                if($item->parent_id == 0){
                    $m->add($item->title, $item->link)->id($item->id);
                }
                //иначе формируем дочерний пункт меню
                else {
                    //ищем для текущего дочернего пункта меню в объекте меню ($m)
                    //id родительского пункта (из БД)
                    if($m->find($item->parent_id)){
                        $m->find($item->parent_id)->add($item->title, $item->link)->id($item->id);
                    }
                }
            }
        });
        return $mBuilder;
    }

    //Функция построения дерева из массива от Tommy Lacroix
    public static function getTree($dataset) {
        $tree = array();

        foreach ($dataset as $id => &$node) {
            //Если нет вложений
            if (!$node['parent_id']){
                $tree[$id] = &$node;
            }else{
                //Если есть потомки то перебераем массив
                $dataset[$node['parent_id']]['childs'][$id] = &$node;
            }
        }
        return $tree;
    }

    // формирование дерево для select
    public static function getSelectTree()
    {
        $firstItem = ['title' => 'Главное', 'parent_id' => 0];
        $dataset = Menu::where('status', null)
            ->get(['id', 'parent_id', 'title'])
            ->keyBy('id')->toArray();

        $tree = array();

        foreach ($dataset as $id => &$node) {
            //Если нет вложений
            if (!$node['parent_id']){
                $tree[$id] = &$node;
            }else{
                //Если есть потомки то перебераем массив
                $dataset[$node['parent_id']]['-'][$id] = &$node;
            }
        }
        array_unshift($tree, $firstItem);
        return $tree;
    }

    public static function add($fields)
    {
        if (!empty($fields)) {

            $link = null;
            switch ($fields['type']) {
                case 'page': $link = $fields['page_id'].'/page'; break;
                case 'category': $link = $fields['page_id'].'/page'; break;
                default: $link = $fields['url']; break;
            }
            $menu = new Menu();
            $menu->link = $link;
            $menu->param = serialize(['target' => '_self']);
            $menu->fill($fields);
            $menu->save();
        }
    }

    public function edit($fields)
    {
        if (!empty($fields)) {

            $menu = Menu::find($fields['id']);
            $menu->parent_id = $fields['parent_id'];
            $menu->name = $fields['name'];
            $menu->label = $fields['label'];
            $menu->link = $fields['link'];
            $menu->status = $fields['status'];
            $menu->position = $fields['position'];
            $menu->param = $fields['param'];
            $menu->public_date = $fields['public_date'];
            $menu->un_public_date = $fields['un_public_date'];
            $menu->save();
        }
    }

    public function getStatuses()
    {
        return [0 => 'Удален', 1 => 'Опубликован'];
    }

    public function getContentMenu()
    {
        return Menu::where('status', 2)->get();
    }

    public static function typeList()
    {
        return ['category' => 'Категория', 'page' => 'Стриница', 'url' => 'Ссылка'];
    }

    public static function categoryList()
    {
        return Category::pluck('title', 'id')->all();
    }

    public static function pageList()
    {
        return Post::pluck('title', 'id')->all();
    }

    public function getType()
    {

    }

    public function getCategory()
    {

    }

    public function getPage()
    {

    }


}
