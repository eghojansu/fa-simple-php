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
            // inject page to GET global vars
            'get' => true,
            // adjacents
            'adjacents' => 2,
            // ellipsis style
            'ellipsisStyle' => 'color:white;cursor:default',
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
        $params = [$option['var']=>$page];

        return App::$instance->url($option['route'], $params);
    }
}