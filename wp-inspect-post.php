<?php

/*
Plugin Name: WP Inspect Post
Plugin URI: https://github.com/mackenly/wp-inspect-post
Description: Shows post data from the WP API in the editor to make troubleshooting and WP API work easier.
Version: 0.1
Author: Mackenly Jones
Author URI: https://mackenly.com
License: AGPLv3
*/

// Action to create the meta box
add_action( 'add_meta_boxes', 'wp_api_data_meta_box' );

// Callback function to create the meta box
function wp_api_data_meta_box() {
	add_meta_box( 'wp-api-data-meta-box', 'Inspect Post', 'wp_api_data_meta_box_callback');
}

function getPostData($post_id) {
	// Use the WordPress REST API to retrieve the data for the current post
	$response = wp_remote_get( get_site_url() . '/wp-json/wp/v2/posts/' . $post_id, array(
		// Disable SSL verification to avoid errors with local development environments
		'sslverify' => false,
	) );

	// Check for errors
	if ( is_wp_error( $response ) ) {
		echo 'Error retrieving data';
		echo $response->get_error_message();

		return;
	}

	// Get the data
	return json_decode( wp_remote_retrieve_body( $response ) );
}

function getAuthorData($author_id) {
	// Use the WordPress REST API to retrieve the data for the current post
	$response = wp_remote_get( get_site_url() . '/wp-json/wp/v2/users/' . $author_id, array(
		// Disable SSL verification to avoid errors with local development environments
		'sslverify' => false,
	) );

	// Check for errors
	if ( is_wp_error( $response ) ) {
		echo 'Error retrieving data';
		echo $response->get_error_message();

		return;
	}

	// Get the data
	return json_decode( wp_remote_retrieve_body( $response ) );
}

function getCategoryData($post_id) {
	// Use the WordPress REST API to retrieve the data for the current post
	$response = wp_remote_get( get_site_url() . '/wp-json/wp/v2/categories?post=' . $post_id, array(
		// Disable SSL verification to avoid errors with local development environments
		'sslverify' => false,
	) );

	// Check for errors
	if ( is_wp_error( $response ) ) {
		echo 'Error retrieving data';
		echo $response->get_error_message();

		return;
	}

	// Get the data
	return json_decode( wp_remote_retrieve_body( $response ) );
}

function getTagData($post_id) {
	// Use the WordPress REST API to retrieve the data for the current post
	$response = wp_remote_get( get_site_url() . '/wp-json/wp/v2/tags?post=' . $post_id, array(
		// Disable SSL verification to avoid errors with local development environments
		'sslverify' => false,
	) );

	// Check for errors
	if ( is_wp_error( $response ) ) {
		echo 'Error retrieving data';
		echo $response->get_error_message();

		return;
	}

	// Get the data
	return json_decode( wp_remote_retrieve_body( $response ) );
}

// Callback function to display the meta box content
function wp_api_data_meta_box_callback( $post ) {
	?>
	<style>
        .tab {overflow: hidden;border: 1px solid #ccc;background-color: #f1f1f1;}
        .tab button {background-color: inherit;float: left;border: none;outline: none;cursor: pointer;padding: 12px 16px;transition: 0.3s;}
        .tab button:hover {background-color: #ddd;}
        .tab button.active {background-color: #ccc;}
        .tabcontent {display: none;padding: 6px 12px;border: 1px solid #ccc;border-top: none;}
	</style>
	<div class="tab">
		<button class="tablinks" onclick="openCity(event, 'PostData')">Post Data</button>
		<button class="tablinks" onclick="openCity(event, 'AuthorData')">Author Data</button>
		<button class="tablinks" onclick="openCity(event, 'CategoryData')">Category Data</button>
		<button class="tablinks" onclick="openCity(event, 'TagData')">Tag Data</button>
		<button class="tablinks" onclick="openCity(event, 'SiteData')">Site Data</button>
	</div>

	<div id="PostData" class="tabcontent" style="display: block;">
		<?php
			// Get the data
			$data = getPostData($post->ID);
		?>
		<h3>Post Data</h3>
		<hr>
		<p>
			Title:
			<a href="<?php echo $post->link ?>" target="_blank"><?php echo $data->title->rendered ?></a>
		</p>
		<p>Status: <?php echo $data->status ?></p>
		<p>Created: <?php echo $data->date ?></p>
		<p>Modified: <?php echo $data->modified ?></p>
		<p>Age: <?php echo human_time_diff( strtotime( $data->date ), current_time( 'timestamp' ) ) ?></p>
		<p>Last Modified: <?php echo human_time_diff( strtotime( $data->modified ), current_time( 'timestamp' ) ) ?></p>
		<p>
			ID:
			<a href="<?php echo get_site_url() . '/wp-json/wp/v2/posts/' . $post->ID ?>" target="_blank"><?php echo $data->id ?></a>
		</p>
		<p>Slug: <?php echo $data->slug ?></p>
		<a href="<?php echo $post->link ?>" target="_blank">View Page</a> | <a href="<?php echo get_site_url() . '/wp-json/wp/v2/posts/' . $post->ID ?>" target="_blank">Open Page in API</a>
		<br><br>
		<details>
			<summary>View Page JSON</summary>
			<pre><?php echo json_encode( $data, JSON_PRETTY_PRINT ) ?></pre>
		</details>
	</div>

	<div id="AuthorData" class="tabcontent">
		<?php
		// Get the data
		$authorData = getAuthorData($post->post_author);
		?>
		<h3>Author Data</h3>
		<hr>
		<p>
			Name:
			<a href="<?php echo $authorData->link ?>" target="_blank"><?php echo $authorData->name ?></a>
		</p>
		<p>Description: <?php echo $authorData->description ?></p>
		<p>
			ID:
			<a href="<?php echo get_site_url() . '/wp-json/wp/v2/posts/' . $authorData->ID ?>" target="_blank"><?php echo $authorData->id ?></a>
		</p>
		<p>Slug: <?php echo $authorData->slug ?></p>
		<a href="<?php echo $authorData->link ?>" target="_blank">View Author</a> | <a href="<?php echo get_site_url() . '/wp-json/wp/v2/users/' . $authorData->id ?>" target="_blank">Open Author in API</a>
		<br><br>
		<details>
			<summary>View Author JSON</summary>
			<pre><?php echo json_encode( $authorData, JSON_PRETTY_PRINT ) ?></pre>
		</details>
	</div>

	<div id="CategoryData" class="tabcontent">
		<?php
		// Get the data
		$categoryData = getCategoryData($post->ID);
		?>
		<h3>Category Data</h3>
		<hr>
		<?php
			// loop over the categories
			foreach ($categoryData as $category) {
				?>
				<p>
					Name:
					<a href="<?php echo $category->link ?>" target="_blank"><?php echo $category->name ?></a>
				</p>
				<p>Description: <?php echo $category->description ?></p>
				<p>
					ID:
					<a href="<?php echo get_site_url() . '/wp-json/wp/v2/categories/' . $category->ID ?>" target="_blank"><?php echo $category->id ?></a>
				</p>
				<p>Count: <?php echo $category->count ?></p>
				<p>Slug: <?php echo $category->slug ?></p>
				<p>Taxonomy: <?php echo $category->taxonomy ?></p>
				<p>
					Parent:
					<a href="<?php echo get_site_url() . '/wp-json/wp/v2/categories/' . $category->parent ?>" target="_blank"><?php echo $category->id ?></a>
				</p>
				<a href="<?php echo $category->link ?>" target="_blank">View "<?php echo $category->name ?>"</a> | <a href="<?php echo get_site_url() . '/wp-json/wp/v2/categories/' . $category->id ?>" target="_blank">Open "<?php echo $category->name ?>" in API</a>
				<br>
				<hr>
				<?php
			}
		?>
		<br>
		<details>
			<summary>View Categories JSON</summary>
			<pre><?php echo json_encode( $categoryData, JSON_PRETTY_PRINT ) ?></pre>
		</details>
	</div>

	<div id="TagData" class="tabcontent">
		<?php
		// Get the data
		$tagData = getCategoryData($post->ID);
		?>
		<h3>Tag Data</h3>
		<hr>
		<?php
		// loop over the categories
		foreach ($tagData as $tag) {
			?>
			<p>
				Name:
				<a href="<?php echo $tag->link ?>" target="_blank"><?php echo $tag->name ?></a>
			</p>
			<p>Description: <?php echo $tag->description ?></p>
			<p>
				ID:
				<a href="<?php echo get_site_url() . '/wp-json/wp/v2/tags/' . $tag->ID ?>" target="_blank"><?php echo $tag->id ?></a>
			</p>
			<p>Count: <?php echo $tag->count ?></p>
			<p>Slug: <?php echo $tag->slug ?></p>
			<p>Taxonomy: <?php echo $tag->taxonomy ?></p>
			<p>
				Parent:
				<a href="<?php echo get_site_url() . '/wp-json/wp/v2/tags/' . $tag->parent ?>" target="_blank"><?php echo $tag->id ?></a>
			</p>
			<a href="<?php echo $tag->link ?>" target="_blank">View "<?php echo $tag->name ?>"</a> | <a href="<?php echo get_site_url() . '/wp-json/wp/v2/tags/' . $tag->id ?>" target="_blank">Open "<?php echo $tag->name ?>" in API</a>
			<br>
			<hr>
			<?php
		}
		?>
		<br>
		<details>
			<summary>View Tags JSON</summary>
			<pre><?php echo json_encode( $tagData, JSON_PRETTY_PRINT ) ?></pre>
		</details>
	</div>

	<div id="SiteData" class="tabcontent">
		<h3>Site Data</h3>
        <p>🛑 Warning: The following information contains potentially sensitive configuration data and could be valuable to malicious threat actors. Do not share the contents of this page except with trusted individuals and disable or uninstall this plugin when not in use.</p>
        <hr>
        <h4>Site Info</h4>
        <p>Name: <?php echo get_bloginfo('name') ?></p>
        <p>Description: <?php echo get_bloginfo('description') ?></p>
        <p>Language: <?php echo get_bloginfo('language') ?></p>
        <p>Charset: <?php echo get_bloginfo('charset') ?></p>
        <p>WordPress Version: <?php echo get_bloginfo('version') ?></p>
        <p>HTML Type: <?php echo get_bloginfo('html_type') ?></p>
        <p>Text Direction: <?php echo get_bloginfo('text_direction') ?></p>
        <p>Admin Email: <a href="mailto:<?php echo get_bloginfo('admin_email') ?> target="_blank"><?php echo get_bloginfo('admin_email') ?></a> </p>

        <h4>Site URLs</h4>
        <p>Home URL: <a href="<?php echo get_bloginfo('home') ?>" target="_blank"><?php echo get_bloginfo('home') ?></a></p>
        <p>URL: <a href="<?php echo get_bloginfo('url') ?>" target="_blank"><?php echo get_bloginfo('url') ?></a></p>
        <p>WP URL: <a href="<?php echo get_bloginfo('wpurl') ?>" target="_blank"><?php echo get_bloginfo('wpurl') ?></a></p>
        <p>RSS URL: <a href="<?php echo get_bloginfo('rss_url') ?>" target="_blank"><?php echo get_bloginfo('rss_url') ?></a></p>
        <p>RSS2 URL: <a href="<?php echo get_bloginfo('rss2_url') ?>" target="_blank"><?php echo get_bloginfo('rss2_url') ?></a></p>
        <p>Atom URL: <a href="<?php echo get_bloginfo('atom_url') ?>" target="_blank"><?php echo get_bloginfo('atom_url') ?></a></p>
        <p>Comments RSS2 URL: <a href="<?php echo get_bloginfo('comments_rss2_url') ?>" target="_blank"><?php echo get_bloginfo('comments_rss2_url') ?></a></p>
        <p>Pingback URL: <a href="<?php echo get_bloginfo('pingback_url') ?>" target="_blank"><?php echo get_bloginfo('pingback_url') ?></a></p>
        <p>Stylesheet URL: <a href="<?php echo get_bloginfo('stylesheet_url') ?>" target="_blank"><?php echo get_bloginfo('stylesheet_url') ?></a></p>
        <p>Stylesheet Directory: <a href="<?php echo get_bloginfo('stylesheet_directory') ?>" target="_blank"><?php echo get_bloginfo('stylesheet_directory') ?></a></p>
        <p>Template Directory: <a href="<?php echo get_bloginfo('template_directory') ?>" target="_blank"><?php echo get_bloginfo('template_directory') ?></a></p>
        <p>Template URL: <a href="<?php echo get_bloginfo('template_url') ?>" target="_blank"><?php echo get_bloginfo('template_url') ?></a></p>

        <h4>Server Specs</h4>
        <p>PHP Version: <?php echo phpversion() ?></p>
        <p>Server Software: <?php echo $_SERVER['SERVER_SOFTWARE'] ?></p>
        <p>Server Protocol: <?php echo $_SERVER['SERVER_PROTOCOL'] ?></p>
        <p>Server Name: <?php echo $_SERVER['SERVER_NAME'] ?></p>
        <p>Server Port: <?php echo $_SERVER['SERVER_PORT'] ?></p>
        <p>Server Address: <?php echo $_SERVER['SERVER_ADDR'] ?></p>
        <p>Remote Address: <?php echo $_SERVER['REMOTE_ADDR'] ?></p>
        <p>Document Root: <?php echo $_SERVER['DOCUMENT_ROOT'] ?></p>
        <p>Current Page: <?php echo $_SERVER['PHP_SELF'] ?></p>
        <p>Script Name: <?php echo $_SERVER['SCRIPT_NAME'] ?></p>
        <p>Script Filename: <?php echo $_SERVER['SCRIPT_FILENAME'] ?></p>
        <p>Path Translated: <?php echo $_SERVER['PATH_TRANSLATED'] ?></p>
        <p>PHP Self: <?php echo $_SERVER['PHP_SELF'] ?></p>
        <p>Request Time: <?php echo $_SERVER['REQUEST_TIME'] ?></p>
        <p>Request Time Float: <?php echo $_SERVER['REQUEST_TIME_FLOAT'] ?></p>
        <p>Query String: <?php echo $_SERVER['QUERY_STRING'] ?></p>
        <p>HTTP Accept: <?php echo $_SERVER['HTTP_ACCEPT'] ?></p>
        <p>HTTP Accept Encoding: <?php echo $_SERVER['HTTP_ACCEPT_ENCODING'] ?></p>
        <p>HTTP Accept Language: <?php echo $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?></p>
        <p>HTTP Connection: <?php echo $_SERVER['HTTP_CONNECTION'] ?></p>
        <p>HTTP Host: <?php echo $_SERVER['HTTP_HOST'] ?></p>
        <p>HTTP Referer: <?php echo $_SERVER['HTTP_REFERER'] ?></p>
        <p>HTTP User Agent: <?php echo $_SERVER['HTTP_USER_AGENT'] ?></p>
        <p>HTTPS: <?php echo $_SERVER['HTTPS'] ?></p>
        <p>Remote Port: <?php echo $_SERVER['REMOTE_PORT'] ?></p>
        <p>Server Signature: <?php echo $_SERVER['SERVER_SIGNATURE'] ?></p>
        <p>Path: <?php echo $_SERVER['PATH'] ?></p>
        <p>WINDIR: <?php echo $_SERVER['WINDIR'] ?></p>
        <p>SERVER_SOFTWARE: <?php echo $_SERVER['SERVER_SOFTWARE'] ?></p>
        <p>SERVER_NAME: <?php echo $_SERVER['SERVER_NAME'] ?></p>
        <p>SERVER_ADDR: <?php echo $_SERVER['SERVER_ADDR'] ?></p>
        <p>SERVER_PORT: <?php echo $_SERVER['SERVER_PORT'] ?></p>
        <p>REMOTE_ADDR: <?php echo $_SERVER['REMOTE_ADDR'] ?></p>
        <p>DOCUMENT_ROOT: <?php echo $_SERVER['DOCUMENT_ROOT'] ?></p>
        <p>SCRIPT_FILENAME: <?php echo $_SERVER['SCRIPT_FILENAME'] ?></p>
        <p>REMOTE_PORT: <?php echo $_SERVER['REMOTE_PORT'] ?></p>
        <p>GATEWAY_INTERFACE: <?php echo $_SERVER['GATEWAY_INTERFACE'] ?></p>
        <p>SERVER_PROTOCOL: <?php echo $_SERVER['SERVER_PROTOCOL'] ?></p>
        <p>REQUEST_METHOD: <?php echo $_SERVER['REQUEST_METHOD'] ?></p>
        <p>QUERY_STRING: <?php echo $_SERVER['QUERY_STRING'] ?></p>
        <p>REQUEST_URI: <?php echo $_SERVER['REQUEST_URI'] ?></p>
        <p>SCRIPT_NAME: <?php echo $_SERVER['SCRIPT_NAME'] ?></p>
    </div>

	<script>
        function openCity(evt, cityName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(cityName).style.display = "block";
            evt.currentTarget.className += " active";
        }
	</script>
	<?php
}