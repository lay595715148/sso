<?php
namespace sso\filter;

use lay\core\Filter;

class UserExistsFilter implements Filter {
    public function initilize($filterConfig) {
        
    }
    /**
     * (non-PHPdoc)
     * @see \lay\core\Filter::doFilter()
     */
    public function doFilter($action, $chain) {
        $chain->doFilter($action, $chain);
    }
}
?>
