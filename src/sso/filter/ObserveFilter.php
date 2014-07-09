<?php
namespace sso\filter;

use lay\core\Filter;

class ObserveFilter implements Filter {
    public function initilize($filterConfig) {
        
    }
    public function doFilter($action, $chain) {
        $chain->doFilter();
    }
}
?>
