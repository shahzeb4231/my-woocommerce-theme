<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="s-<?php echo esc_attr( wp_unique_id() ); ?>">Search</label>
	<input type="search" name="s" placeholder="Search..." value="<?php echo esc_attr( get_search_query() ); ?>">
	<button class="btn btn-primary" type="submit">Search</button>
</form>
