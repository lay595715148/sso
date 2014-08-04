<?php
namespace sso\filter;

use lay\core\Filter;
use lay\core\FilterChain;
use lay\core\Action;

class ObserveFilter implements Filter {
    public function initilize($config) {
        
    }
    /**
     * @param Action $chain
     * @param FilterChain $chain
     */
    public function doFilter($action, $chain) {
        $chain->doFilter($action);
    }
}
?>
