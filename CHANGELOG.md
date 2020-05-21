# Changelog

## [Unreleased]

- Redirect new user to Welcome Page where they can set up Motto and Headshot
- Add / Edit Player Headshot: Need to find storage option. AWS?
- Recover account / Reset password
- Restrict access to recovery page if user is currently logged in
- Create `Change Password` for in player profile
- Add 'Edit / Delete' popout when user clicks edit button next to a hit

## [Bugs]

- Would like to figure out how to return 0 for stat type at sql level

## [0.0.8] - 2020-05-20

## Added

- Validation on Registration form. Form will display relevant errors if certain fields are not filled out correctly

## Changed

- Registration form design. Using `form_widget` to build up the form allowing me to add attributes such as classes
- JS used to make Login or Sign Up button active, now looks at all fields within current form to verify they all have content
- Twig Templates modified for styling

## [0.0.7] - 2020-05-06

## Added

- Design implementation started using `@symfony/webpack-encore`

## [0.0.6] - 2020-05-01

## Added

- Reset Password

## [0.0.5] - 2020-05-01

## Added

- Add / Edit Player Motto
- Edit / Delete a hit
- [SendEmail.php](/src/Services/SendEmail.php) added as a service. File used to send emails via SendGrid
- SendGrid API key added to `services.yaml` to be passed as argument to SendMail service
- Add player mottos to Leaderboard page
- Method added to [MottoRepository.php](/src/Repository/MottoRepository.php) `findMotto` that returns the player motto or null if not found. This way I don't have to duplicate `if null` checking in all Controllers that require a player motto
- New route added to [AddScoreController.php](/src/Controller/AddScoreController.php) to edit scores. May want to rename `AddScore` files since they will also Updated and Delete

## [0.0.4] - 2020-04-25

## Added

- Created Player Profile to display stats; Point Breakdown, Hit History and Top Targets
- UserController
- Custom queries added to StatsRepository `getBreakdown` and `getTopTargets`

## Changed

- Date column in Stats Entity changed to `datetime`

## [0.0.3] - 2020-04-22

## Added

- LeagueController
- SearchLeagueController
- League page to display all the current leagues with links to their leaderboard
- League page displays all players in that league and their current scores. Scores are sorted highest to lowest
- When a user logs in they are automatically redirected to their leagues page instead of the home page
- After a player submits a score they are also redirected to their leagues page instead of the home page

## Fixed

- Query to return players in "Add Score" form needed update so that it only returned the players in the same league as the currently logged in player. Previously it returned all players

## [0.0.2] - 2020-04-21

## Added

- Stats Entity
- Stats Repository
- AddScoreController
- AddScoreType form. Added query to omit currently logged in user from dropdown
- Checks during Sign Up ensuring username or emails doesn't already exists and that the league they are joining exists. If checks don't page user is presented with an error message

## Removed

- Original Entity Associations removed. They were incorrect.

## Fixed

- Entity Associations. New Associations set up for User to reference League table for League ID and Stats to reference User table for User ID

## Changed

- Login twig template naming update 'security' -> 'login'
- Time field for scoring changed to date. The need to enter a time seems overkill. Date alone will be sufficient

## [0.0.1] - 2020-04-20

### Added

- Basic Sign Up, Login, Logout functionality
- README
- .htaccess for rewrite to `/index.php`
- .env.local - configured for local database credentials
- .php_cs.dist
- Packages installed
  - `symfony/apache-pack`
  - `symfony/form`
  - `symfony/orm-pack`
  - `symfony/security-bundle`
  - `symfony/twig-bundle`
  - `symfony/maker-bundle` _dev package_
