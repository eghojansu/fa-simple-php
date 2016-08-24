<?php

/**
 * HTML Helper
 */
class HTML
{
    /**
     * Get notifier html markup
     * @param  string $type
     * @param  string $message
     * @return string
     */
    public function notify($type, $message)
    {
        return $message?<<<NOTIFY
<div class="hide notifier" data-class-name="$type">
$message
</div>
NOTIFY
: null;
    }

    /**
     * Bootstrap alert
     * @param  string $type
     * @param  string $message
     * @param  string $head
     * @return string
     */
    public function alert($type, $message, $head = null)
    {
        $head = $head?"<strong>$head</strong>":$head;

        return $message?<<<NOTIFY
<div class="alert alert-$type alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  $head $message
</div>
NOTIFY
: null;
    }

    /**
     * Construct ul bootstrap navbar structure
     * Only support 2-level list
     * @param  array  $items
     *         [
     *             'path'=>'path',
     *             'label'=>'Path',
     *             'roles'=>[],
     *             'items'=>[
     *                 [
     *                     'path'=>'path',
     *                     'label'=>'Path',
     *                     'roles'=>[],
     *                 ]
     *             ],
     *         ]
     * @param  string $currentPath
     * @param  array  $option @see source
     * @return string
     */
    public function navbarNav(array $items, $currentPath = null, array $option = [])
    {
        $option = array_replace_recursive([
            // ul class
            'class' => 'nav navbar-nav',
            // append ul class
            'appendClass' => '',
            // ul > li attr
            'parentAttr' => [
                'class' => 'dropdown',
            ],
            // ul > li > a attr
            'parentItemAttr' => [
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown',
                'role' => 'button',
                'aria-haspopup' => 'true',
                'aria-expanded' => 'false',
            ],
            // ul > li > ul attr
            'childGroupAttr' => [
                'class' => 'dropdown-menu',
            ],
            // ul > li > ul > li
            'childAttr' => [
            ],
            // ul > li > ul > li > a
            'childItemAttr' => [
            ],
            'useCaret'=>true,
        ], $option);

        $role = App::instance()->service('user')->get('role');
        $str = '';
        foreach ($items as $item) {
            $item += [
                'path' => null,
                'label' => null,
                'items' => [],
                'roles' => [],
            ];
            if ($item['roles'] && !in_array($role, $item['roles'])) {
                continue;
            }
            $list = '';
            $strChild = '';
            $active = $currentPath === $item['path'];
            $parentAttr = [];
            $parentItemAttr = [];
            $childGroupAttr = $option['childGroupAttr'];
            $childAttr = $option['childAttr'];
            $childItemAttr = $option['childItemAttr'];
            $childCounter = 0;

            if (count($item['items'])) {
                $activeFromChild = false;
                foreach ($item['items'] as $child) {
                    $child += [
                        'path' => null,
                        'label' => null,
                        'roles' => [],
                    ];

                    if ($child['roles'] && !in_array($role, $child['roles'])) {
                        continue;
                    }

                    $childCounter++;
                    $childActive = $currentPath === $child['path'];
                    if (!$activeFromChild) {
                        $activeFromChild = $childActive;
                        $active = $activeFromChild;
                    }
                    $url = '#'===$child['path']?'#':App::instance()->url($child['path']);
                    $strChild .= '<li'
                              . $this->renderAttributes($childAttr, ['class'=>$childActive?'active':''])
                              . '>'
                              . '<a'
                              . $this->renderAttributes(['href'=>$url]+$childItemAttr)
                              . '>'
                              . $child['label']
                              . '</a>'
                              . '</li>';
                }
                if ($childCounter) {
                    $parentAttr += $option['parentAttr'];
                    $parentItemAttr += $option['parentItemAttr'];
                    $strChild = '<ul'
                              . $this->renderAttributes($childGroupAttr)
                              . '>'
                              . $strChild
                              . '</ul>';
                    if ($option['useCaret']) {
                        $item['label'] .= ' <span class="caret"></span>';
                    }
                } else {
                  $strChild = '';
                }
            }

            if (count($item['items']) && 0 === $childCounter) {
                continue;
            }
            $url = '#'===$item['path']?'#':App::instance()->url($item['path']);
            $str .= '<li'
                 . $this->renderAttributes($parentAttr, ['class'=>$active?'active':''])
                 . '>'
                 . '<a'
                 . $this->renderAttributes(['href'=>$url]+$parentItemAttr)
                 . '>'
                 . $item['label']
                 . '</a>'
                 . $strChild
                 . '</li>';
        }
        $str = '<ul'
             . $this->renderAttributes(['class'=>$option['class']], ['class'=>$option['appendClass']])
             . '>'
             . $str
             . '</ul>';

        return $str;
    }

    /**
     * Generate pagination based on subset array
     * returned by Database::paginate
     * @param  array  $subset
     * @param  array  $option
     * @return string
     */
    public function pagination(array $subset, array $option = [])
    {
        $option += [
            // ul class
            'class' => 'pagination pagination-sm',
            // append class
            'appendClass' => 'pull-right',
            // current page
            'page' => $subset['page'],
            // total pages
            'max' => $subset['total'],
            // route name
            'route' => null,
            // role
            'role' => 'admin',
            // route param
            'params' => [],
            // index in route param
            'var' => 'page',
            // adjacents
            'adjacents' => 2,
            // ellipsis style
            'ellipsisStyle' => 'cursor:default',
        ];

        extract($option);

        $page *= 1;

        $str = '';
        if ($max > 1) {
            $isFirstPage = $page <= 1;
            $str .= '<li'.($isFirstPage?' class="disabled"':'').'>'
                 .  '<a href="'.($isFirstPage?'#':$this->paginationHref($option, 1)).'">&laquo;</a>'
                 .  '</li>';

            $str .= '<li'.($isFirstPage?' class="disabled"':'').'>'
                 .  '<a href="'.($isFirstPage?'#':$this->paginationHref($option, $page-1<1?1:$page-1)).'">&lsaquo;</a>'
                 .  '</li>';

            $start = ($page <= $adjacents ? 1 : $page - $adjacents);
            $end   = ($page > $max - $adjacents ? $max : $page + $adjacents);

            if ($start > 1) {
                $str .= '<li><a style="'.$ellipsisStyle.'">...</a></li>';
            }

            for($i= $start; $i <= $end; $i++) {
                $active = $i === $page;
                $str .= '<li'.($active?' class="active"':'').'>'
                     .  '<a href="'.($active?'#':$this->paginationHref($option, $i)).'">'.$i.'</a>'
                     . '</li>';
            }

            if ($end < $max) {
                $str .= '<li><a style="'.$ellipsisStyle.'">...</a></li>';
            }

            $isMaxPage = $page >= $max;
            $str .= '<li'.($isMaxPage?' class="disabled"':'').'>'
                 .  '<a href="'.($isMaxPage?'#':$this->paginationHref($option, $page+1<=$max?$page+1:$page)).'">&rsaquo;</a>'
                 .  '</li>';

            $str .= '<li'.($isMaxPage?' class="disabled"':'').'>'
                 .  '<a href="'.($isMaxPage?'#':$this->paginationHref($option, $max)).'">&raquo;</a>'
                 .  '</li>';
        }

        return '<ul class="'.$class.' '.$appendClass.'">'.$str.'</ul>';
    }

    /**
     * Pagination href, for use in self::pagination
     * @param  array  $option
     * @param  int $page
     * @return string
     */
    protected function paginationHref(array $option, $page)
    {
        $params = [$option['var']=>$page]+($_GET?:[]);

        return App::instance()->url($option['route'], $params);
    }

    protected function renderAttributes(array $attr, array $append = [])
    {
        foreach ($append as $key => $value) {
            if (!$value) {
                continue;
            } elseif (isset($attr[$key])) {
                $attr[$key] .= ' '.trim($value);
            } else {
                $attr[$key] = $value;
            }
        }
        $str = '';
        foreach ($attr as $key => $value) {
            $str .= ' '.$key.(''===$value?'':'="'.$value.'"');
        }

        return $str;
    }
}
