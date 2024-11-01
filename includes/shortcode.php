<?php

if (!class_exists('TapchaShortcode')) {
    class TapchaShortcode {
        function render() {
            $site_key = esc_attr(get_option("tapcha-site-key"));

            return '
                <div class="form-signin">                
                    <div class="alert alert-success alert-dismissible fade show d-none" role="alert" id="alert-success">
                        ' . __('Passed Tapcha test!', 'tapcha') . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="' . __('Close', 'tapcha') . '">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                
                    <div class="alert alert-danger alert-dismissible d-none" role="alert" id="alert-error">
                        ' . __('Failed Tapcha test!', 'tapcha') . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="' . __('Close', 'tapcha') . '">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                
                    <div class="alert alert-danger alert-dismissible d-none" role="alert" id="alert-challenge-consumed">
                        ' . __('This challenge has already been consumed', 'tapcha') . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="' . __('Close', 'tapcha') . '">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                
                    <div class="alert alert-danger alert-dismissible d-none" role="alert" id="alert-invalid-challenge-id">
                        ' . __('The challenge id is invalid') . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="' . __('Close' , 'tapcha') . '">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                
                    <div class="alert alert-danger alert-dismissible d-none" role="alert" id="alert-invalid-challenge-answer">
                        ' . __('The answer is in the wrong format', 'tapcha') . '
                        <button type="button" class="close" data-dismiss="alert" aria-label="' . __('Close', 'tapcha') . '">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                                
                    <div id="tapcha">
                        <div id="imageSection">
                            <img id="image" class="img-responsive" src="" alt="' . __('Tapcha instructions', 'tapcha') . '">
                        </div>
                        <div id="tapcha-canvas"></div>
                    </div>
                
                    <div id="tapcha-loading" class="spinner spinner-border"></div>
                    
                    <p id="tapcha-error-invalid-site-key" class="d-none">' . __('Invalid site key!', 'tapcha') . '</p>
                    <p id="tapcha-error" class="d-none">' . __('Error! Please contact the site administrator.', 'tapcha') . '</p>
                    
                    <button class="btn btn-lg btn-secondary btn-block" id="tapcha-reload">
                        ' . __('Reload', 'tapcha') . '
                    </button>
                           
                    <button class="btn btn-lg btn-primary btn-block" type="submit" id="tapcha-submit">
                        <span id="tapcha-submitLoading" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span id="tapcha-submitText">' . __('Submit', 'tapcha') . '</span>
                    </button>
                                    
                    <input type="hidden" name="tapcha-site-key" id="tapcha-site-key" value="' . $site_key . '">
                    <input type="hidden" name="tapcha-challenge-id" id="tapcha-challenge-id">
                    <input type="hidden" name="tapcha-challenge-answer" id="tapcha-challenge-answer">
                </span>
                </div>
            ';
        }
    }
}