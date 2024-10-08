#!/bin/bash

# In WP_UnitTestCase_Base class::set_up, existing files in uploaded directory set as ignored files if $ignore_files is empty,
# And stored in a static variable $ignore_files. To do this, class scans the uploads directory.
# If the uploads directory does not exist, the class will not be able to scan the directory and the test will pass with errors.
# This script is used to create uploads directory and set read-write permissions for the uploads directory.

# Function to check if the WordPress directory exists
is_wordpress_directory_present() {
    [ -d ./vendor/wordpress ]
}

# Function to check if the uploads directory exists
is_uploads_directory_present() {
    [ -d ./vendor/wordpress/wordpress/src/wp-content/uploads ]
}

# Function to create the uploads directory and set read-write permissions
create_and_set_permissions_for_uploads_directory() {
    mkdir -p ./vendor/wordpress/wordpress/src/wp-content/uploads
    chmod -R 777 ./vendor/wordpress/wordpress/src/wp-content/uploads
}

# Main function to orchestrate the script execution
main() {
    if is_wordpress_directory_present; then
        if ! is_uploads_directory_present; then
            create_and_set_permissions_for_uploads_directory
        fi
    fi
}

main
