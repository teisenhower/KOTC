# Changelog

## [Unreleased]

- Create Player Profile

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
- AddScoreType form. Added query to ommit currently logged in user from dropdown
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
