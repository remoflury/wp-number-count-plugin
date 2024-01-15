<?php
/**
 * Plugin Name: Number Count Plugin
 * Description: A simple plugin to insert a number and increase or decrease it by one.
 * Version: 1.0
 * Author: Remo Flury
 */

// Activation Hook
register_activation_hook(__FILE__, 'snp_activate');
function snp_activate() {
    add_option('snp_number', 0); // Initial value is 0
}

// Create admin menu
add_action('admin_menu', 'snp_menu');
function snp_menu() {
    add_menu_page('Simple Number Plugin', 'Simple Number', 'manage_options', 'simple_number_plugin', 'snp_page');
}

// Admin page content
function snp_page() {
    $number = get_option('snp_number');
    ?>
    <div class="wrap">

        <h1>Simple Number Plugin</h1>
        <form method="post" action="" id="number-form">
            <input type="number" name="snp_number" id="number" min="0" steps="1" value="<?php echo $number; ?>">
            <input type="submit" name="snp_update" value="Update Number">
        </form>
        <script type="text/javascript" defer>
          const numberInputElem = document.querySelector('#number')
          const formElem = document.querySelector('#number-form')

          formElem.addEventListener('submit', (event) => {
            
            localStorage.setItem("visitorCount", numberInputElem.value);
            
          })

          document.addEventListener('DOMContentLoaded', () => {
            const visitorCount = localStorage.getItem("visitorCount");
            document.querySelector('#number').value = visitorCount
          })
        </script>
    </div>
    <?php
    if(isset($_POST['snp_update'])) {
        update_option('snp_number', $_POST['snp_number']);
    }
}

// Register REST API endpoint
add_action('rest_api_init', function () {
  register_rest_route('snp/v1', '/number/', array(
      'methods' => 'GET',
      'callback' => 'snp_get_number',
  ));
  register_rest_route('snp/v1', '/number/update/', array(
      'methods' => 'POST',
      'callback' => 'snp_update_number',
  ));
});

function snp_get_number() {
  return get_option('snp_number');
}

function snp_update_number(WP_REST_Request $request) {
  $new_value = $request->get_param('new_value');
  update_option('snp_number', $new_value);
  return $new_value;
}

