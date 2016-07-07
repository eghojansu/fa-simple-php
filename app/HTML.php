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
        ], $option);

        $str = '';
        foreach ($items as $item) {
            $item += [
                'path' => null,
                'label' => null,
                'items' => [],
            ];
            $list = '';
            $strChild = '';
            $active = $currentPath === $item['path'];
            $parentAttr = [];
            $parentItemAttr = [];
            $childGroupAttr = $option['childGroupAttr'];
            $childAttr = $option['childAttr'];
            $childItemAttr = $option['childItemAttr'];

            if (count($item['items'])) {
                $parentAttr += $option['parentAttr'];
                $parentItemAttr += $option['parentItemAttr'];

                $activeFromChild = false;
                foreach ($item['items'] as $child) {
                    $child += [
                        'path' => null,
                        'label' => null,
                    ];
                    $childActive = $currentPath === $child['path'];
                    if (!$activeFromChild) {
                        $activeFromChild = $childActive;
                        $active = $activeFromChild;
                    }
                    $url = '#'===$child['path']?'#':App::$instance->url($child['path']);
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
                $strChild = '<ul'
                          . $this->renderAttributes($childGroupAttr)
                          . '>'
                          . $strChild
                          . '</ul>';
                $item['label'] .= ' <span class="caret"></span>';
            }

            $url = '#'===$item['path']?'#':App::$instance->url($item['path']);
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
            $isFirstPage = 1 === $page;
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

            $isMaxPage = $max === $page;
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

        return App::$instance->url($option['route'], $params);
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