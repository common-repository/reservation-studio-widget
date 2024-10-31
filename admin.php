<?php

use Rakit\Validation\Validator;

require('vendor/autoload.php');
include_once 'config.php';
include_once 'updater.php';
global $rules, $availableLanguages, $positions, $defaultValues;

// XSS clean POST
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

$errors = [];

if (isset($_POST['reset'])) {
    foreach ($defaultValues as $key => $value) {
        $hasOption = get_option('rs_widget_' . $key);
        if ($hasOption === false) {
            add_option('rs_widget_' . $key, $value);
        } else {
            update_option('rs_widget_' . $key, $value);
        }
    }
    $_SESSION['rsWidgetSave']['success_message'] = 'The settings was reset successfully!';

    pluginVersionUpdate();
}

if (isset($_POST['widget_save'])) {

    if (!isset($_POST['rs_nonce']) || !wp_verify_nonce($_POST['rs_nonce'], 'rs_nonce')) {
        die('Nonce error!');
    }

    $validator = new Validator();
    $validation = $validator->make($_POST, $rules);
    $validation->validate();

    if ($validation->fails()) {
        $errors = $validation->errors()->firstOfAll();
    } else {
        foreach ($validation->getValidData() as $key => $value) {
            $hasOption = get_option('rs_widget_' . $key);
            if ($hasOption === false) {
                add_option('rs_widget_' . $key, $value);
            } else {
                update_option('rs_widget_' . $key, $value);
            }
        }
        $_SESSION['rsWidgetSave']['success_message'] = 'The data was saved successfully!';
    }

    pluginVersionUpdate();
} else {
    foreach ($rules as $key => $rule) {
        $_POST[$key] = get_option('rs_widget_' . $key);
        if (empty($_POST[$key]) && isset($defaultValues[$key])) {
            $_POST[$key] = $defaultValues[$key];
        }
    }
}

function getError($key)
{
    global $errors;

    if (!isset($errors[$key])) {
        return '';
    }
    return '<div class="error-message">' . $errors[$key] . '</div>';
}

?>

<link rel="stylesheet" href="<?php echo plugins_url('reservation-studio-widget/assets/css/style.css'); ?>">

<div class="wrap">
    <div class="flex">
        <img height="61px"
             src="<?php echo plugins_url('reservation-studio-widget/assets/images/logo.svg'); ?>"
             class="logo" alt="reservation.studio">

        <div style="margin-left: 20px">
            <h1 style="display: block;">Reservation.Studio Booking widget</h1>
            <a href="https://business.reservation.studio/" target="_blank">Business application</a> |
            <a href="https://reservation.business/" target="_blank">Reservation.Business</a> |
            <a href="https://reservation.studio/" target="_blank">Reservation.Studio</a> |
            <a href="https://help.reservation.studio/" target="_blank">Help center</a>
        </div>
    </div>

    <?php
    if (isset($_SESSION['rsWidgetSave']['success_message'])) {
        echo '<div class="success-message">' . $_SESSION['rsWidgetSave']['success_message'] . '</div>';
        unset($_SESSION['rsWidgetSave']['success_message']);
    }
    ?>

    <form method="post" action="">
        <input name="rs_nonce" type="hidden" value="<?php echo wp_create_nonce('rs_nonce'); ?>"/>

        <h2 class="title">General settings</h2>

        <table class="form-table">
            <tbody>

            <!--Enabled-->
            <tr>
                <th scope="row">
                    <label for="enabled">Enable widget</label>
                </th>
                <td>
                    <input type="checkbox" id="enabled" name="enabled"
                        <?php if (isset($_POST['enabled']) && $_POST['enabled'] == 1) {
                            echo 'checked';
                        } ?>
                           value="1"
                           class="regular-text code">
                    <?php echo getError('enabled'); ?>
                </td>
            </tr>


            <!-- Page type -->
            <tr>
                <th scope="row">
                    <label for="page_type">Page type</label>
                </th>
                <td>
                    <select name="page_type" id="page_type">
                        <option value="location_profile" <?php echo isset($_POST['page_type']) && $_POST['page_type'] == 'location_profile' ? 'selected' : ''; ?>>
                            Location full profile
                        </option>
                        <option value="location_services" <?php echo isset($_POST['page_type']) && $_POST['page_type'] == 'location_services' ? 'selected' : ''; ?>>
                            Location services catalog only (embed)
                        </option>
                        <option value="location_classes" <?php echo isset($_POST['page_type']) && $_POST['page_type'] == 'location_classes' ? 'selected' : ''; ?>>
                            Location classes (embed)
                        </option>
                        <option value="business_profile" <?php echo isset($_POST['page_type']) && $_POST['page_type'] == 'business_profile' ? 'selected' : ''; ?>>
                            Business profile
                        </option>
                    </select>
                    <?php echo getError('page_type'); ?>
                </td>
            </tr>

            <!-- Slug-->
            <tr id="slug-wrapper">
                <th scope="row">
                    <label for="slug">Slug</label>
                </th>
                <td>
                    <input type="text" id="slug" name="slug"
                           value="<?php echo htmlspecialchars($_POST['slug']); ?>"
                           class="regular-text code">
                    <p class="description">
                        Slug of business or location.
                    </p>
                    <?php echo getError('slug'); ?>
                </td>
            </tr>

            <!--Language-->
            <tr>
                <th scope="row">
                    <label for="language">Language</label>
                </th>
                <td>
                    <select name="language" id="language">
                        <?php
                        foreach ($availableLanguages as $key => $value) {
                            echo '<option ' . (isset($_POST['language']) && $_POST['language'] == $key ? 'selected' : '') . ' value="' . $key . '">' . $value . '</option>';
                        }
                        ?>
                    </select>
                    <?php echo getError('language'); ?>
                </td>
            </tr>
            </tbody>
        </table>

        <h2 class="title">Sticky button settings</h2>

        <table class="form-table">
            <tbody>
            <!--Sticky button enabled-->
            <tr>
                <th scope="row">
                    <label for="sticky_button_enabled">Enable sticky button</label>
                </th>
                <td>
                    <input type="checkbox" id="sticky_button_enabled" name="sticky_button_enabled"
                        <?php if (isset($_POST['sticky_button_enabled']) && $_POST['sticky_button_enabled'] == 1) {
                            echo 'checked';
                        } ?>
                           value="1"
                           class="regular-text code">
                    <?php echo getError('sticky_button_enabled'); ?>
                </td>
            </tr>

            <!-- Sticky button text -->
            <tr>
                <th scope="row">
                    <label for="sticky_button_text">Text</label>
                </th>
                <td>
                    <input type="text" id="sticky_button_text" name="sticky_button_text"
                           value="<?php echo htmlspecialchars($_POST['sticky_button_text']); ?>"
                           class="regular-text code">
                    <?php echo getError('sticky_button_text'); ?>
                </td>
            </tr>

            <!-- Sticky button text color -->
            <tr>
                <th scope="row">
                    <label for="sticky_button_text_color">Color</label>
                </th>
                <td>
                    <input type="color" id="sticky_button_text_color" name="sticky_button_text_color"
                           value="<?php echo htmlspecialchars($_POST['sticky_button_text_color']); ?>"
                           class="regular-text code">
                    <?php echo getError('sticky_button_text_color'); ?>
                </td>
            </tr>

            <!-- Sticky button background color -->
            <tr>
                <th scope="row">
                    <label for="sticky_button_background_color">Background color</label>
                </th>
                <td>
                    <input type="color" id="sticky_button_background_color" name="sticky_button_background_color"
                           value="<?php echo htmlspecialchars($_POST['sticky_button_background_color']); ?>"
                           class="regular-text code">
                    <?php echo getError('sticky_button_background_color'); ?>
                </td>
            </tr>

            <!-- Sticky tooltip text -->
            <tr>
                <th scope="row">
                    <label for="sticky_tooltip_text">Tooltip text</label>
                </th>
                <td>
                    <input type="text" id="sticky_tooltip_text" name="sticky_tooltip_text"
                           value="<?php echo htmlspecialchars($_POST['sticky_tooltip_text']); ?>"
                           class="regular-text code">
                    <?php echo getError('sticky_tooltip_text'); ?>
                </td>
            </tr>

            <!-- Sticky tooltip show delay -->
            <tr>
                <th scope="row">
                    <label for="sticky_tooltip_show_delay">Tooltip show delay</label>
                </th>
                <td>
                    <input type="number" id="sticky_tooltip_show_delay" name="sticky_tooltip_show_delay"
                           value="<?php echo htmlspecialchars($_POST['sticky_tooltip_show_delay']); ?>"
                           class="regular-text code">
                    <p class="description">in seconds</p>
                    <?php echo getError('sticky_tooltip_show_delay'); ?>
                </td>
            </tr>

            <!-- Sticky tooltip expire time -->
            <tr>
                <th scope="row">
                    <label for="sticky_tooltip_expire_time">Tooltip show expire time</label>
                </th>
                <td>
                    <input type="number" id="sticky_tooltip_expire_time" name="sticky_tooltip_expire_time"
                           value="<?php echo htmlspecialchars($_POST['sticky_tooltip_expire_time']); ?>"
                           class="regular-text code">
                    <p class="description">in seconds</p>
                    <?php echo getError('sticky_tooltip_expire_time'); ?>
                </td>
            </tr>

            <!--Sticky button position-->
            <tr>
                <th scope="row">
                    <label for="sticky_button_position">Position</label>
                </th>
                <td>
                    <select name="sticky_button_position" id="sticky_button_position">
                        <?php
                        foreach ($positions as $key => $value) {
                            echo '<option ' . (isset($_POST['sticky_button_position']) && $_POST['sticky_button_position'] == $key ? 'selected' : '') . ' value="' . $key . '">' . $value . '</option>';
                        }
                        ?>
                    </select>
                    <?php echo getError('sticky_button_position'); ?>
                </td>
            </tr>

            <tr>

                <th scope="row">
                    <label>Preview</label>
                </th>
                <td>
                    <div id="preview"
                         style="background-image: url('<?php echo plugins_url('reservation-studio-widget/assets/images/laptop.png'); ?>')">
                        <div
                                id="widget"
                                style="background-color: <?php echo $_POST['sticky_button_background_color']; ?>; color: <?php echo $_POST['sticky_button_text_color']; ?>"
                                class="position-<?php echo htmlspecialchars($_POST['sticky_button_position']); ?>"
                        >
                            <?php echo htmlspecialchars($_POST['sticky_button_text']); ?>
                        </div>
                    </div>

                </td>
            </tr>

            </tbody>
        </table>

        <h2 class="title">Modal pop-up settings</h2>

        <table class="form-table">
            <tbody>
            <!-- modal max width -->
            <tr>
                <th scope="row">
                    <label for="modal_max_width">Width of modal</label>
                </th>
                <td>
                    <input type="text" id="modal_max_width" name="modal_max_width"
                           value="<?php echo htmlspecialchars($_POST['modal_max_width']); ?>"
                           class="regular-text code"/>
                    <p class="description">ex: 1320px or 90%. Default value is 1320px.</p>
                    <?php echo getError('modal_max_width'); ?>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="modal_max_height">Height of modal</label>
                </th>
                <td>
                    <input type="text" id="modal_max_height" name="modal_max_height"
                           value="<?php echo htmlspecialchars($_POST['modal_max_height']); ?>"
                           class="regular-text code"/>
                    <p class="description">ex: 800px or 95%. Default value is 95%.</p>
                    <?php echo getError('modal_max_height'); ?>
                </td>
            </tr>

            </tbody>
        </table>

        <h2 class="title">Advance settings</h2>

        <table class="form-table">
            <tbody>
            <!-- buttons_selector -->
            <tr>
                <th scope="row">
                    <label for="buttons_selector">Button element selector</label>
                </th>
                <td>
                    <input type="text" id="buttons_selector" name="buttons_selector"
                           value="<?php echo htmlspecialchars($_POST['buttons_selector']); ?>"
                           class="regular-text code">
                    <p class="description">
                        This is the CSS selector used to select the HTML element(s)
                        on your webpage that will act as the button(s).
                        Different HTML elements can be selected based on their id,
                        class, attribute, etc. using CSS selectors.
                        For example, if you want to select a &lt;a&gt;
                        element with the class "booking-button", use the "."
                        operator followed by the classname: "a.booking-button".
                    </p>
                    <?php echo getError('sticky_button_text'); ?>
                </td>
            </tr>
            </tbody>
        </table>

        <button type="submit" name="widget_save" class="button button-primary">Save Changes</button>
        <button type="submit" name="reset" class="button">Reset defaults</button>
    </form>
</div>

<script>
    var enabled = '<?php echo (isset($_POST['enabled']) && $_POST['enabled'] == 1) ? 1 : 0; ?>';

    var backgroundInput = jQuery('#sticky_button_background_color'),
        textColorInput = jQuery('#sticky_button_text_color'),
        textInput = jQuery('#sticky_button_text'),
        positionInput = jQuery('#sticky_button_position');

    backgroundInput.on('input', function () {
        jQuery('#widget').css('background-color', jQuery(this).val());
    });

    textColorInput.on('input', function () {
        jQuery('#widget').css('color', jQuery(this).val());
    });

    textInput.on('input', function () {
        jQuery('#widget').text(jQuery(this).val());
    });

    positionInput.on('input', function () {
        jQuery('#widget')
            .removeClass()
            .addClass('position-' + jQuery(this).val());
    });
</script>