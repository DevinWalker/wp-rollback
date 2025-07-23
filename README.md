[![WP Rollback](https://wprollback.com/wp-content/uploads/2024/01/WP-Rollback-GitHub.jpg)](https://wprollback.com)

# WP Rollback

Effortlessly rollback (or downgrade, as some may call it) any theme or plugin from WordPress.org to a previous version
with WP Rollback. It's as simple as using the plugin updater, but instead, you downgrade to a specific version. Forget
the hassle of manual downloads and FTP uploads; this plugin streamlines the process for you.

## Important: Usage Disclaimer

Before performing a rollback, we highly recommend conducting a test on a staging site and creating a full backup of your
WordPress files and database. **Please note: We are not liable for any issues such as data loss, white screens, fatal
errors, or other problems that may arise from using this plugin**.

## Download

For stable releases, visit the [WordPress Repository](https://wordpress.org/plugins/wp-rollback). You can also find them
in your WordPress Dashboard under "Plugins > Add New." For development versions, see the contribution section below on
how to clone this repo and start using the latest updates.

## Support

Have questions or need assistance? Post all your support requests on
the [WordPress Repository support page for WP Rollback](https://wordpress.org/support/plugin/wp-rollback). If you'd like
to report bugs, request features, or contribute, we welcome your input!

## Documentation

Designed for seamless integration with the WordPress interface, WP Rollback is straightforward and setting-free. We're
confident that its functionality will be apparent and intuitive right after activation.

[Read the WP Rollback Documentation](https://github.com/impress-org/wp-rollback/wiki)

## Contributing

We appreciate contributions from the community! To contribute:

1. **Fork the Repository**: Click the 'Fork' button at the top right of this page to create your own copy of this
   repository.

2. **Clone Your Fork**: Clone your fork to your local machine. This can be done via the command line with
   `git clone https://github.com/DevinWalker/wp-rollback.git`. Make sure you clone it to the `wp-content/plugins`
   directory of your WordPress installation.

3. **Install Dependencies**: Run `bun install` to install all dependencies.

4. **Available Scripts**:

    - `bun run build` - Create a production build
    - `bun run dev-build` - Start development mode with file watching
    - `bun run generate:pot` - Generate translation files
    - `bun run plugin-zip` - Create a deployable plugin zip file
    - `bun run rm-modules` - Remove node_modules directory

5. **Create a New Branch**: Before making your changes, switch to a new branch with `git checkout -b your-branch-name`.

6. **Make Your Changes**: Implement your changes, enhancements, or bug fixes.

7. **Development**: Run `bun run dev-build` to start the development process. This will watch for changes to the JS and SCSS files and compile them automatically.

8. **Testing**: Before submitting, build the plugin with `bun run build` to ensure everything compiles correctly.

9. **Commit and Push**: Commit your changes with a clear commit message and push them to your fork with
   `git push origin your-branch-name`.

10. **Submit a Pull Request (PR)**: Go to the original WP Rollback repository and click 'New pull request'. Choose your
    fork and branch, then submit the pull request. Provide a decent PR description explaining the changes you made and
    we'll review your PR and merge it if it contributes positively to the project!
