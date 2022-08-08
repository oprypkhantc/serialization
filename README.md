# Good PHP serialization

The concept is similar to Moshi, a rising Java/Kotlin library - serialization with the least 
effort possible without  sacrificing customizability, support for different formats or ease of use.

### Commands
Install dependencies:
`docker run -it --rm -v $PWD:/app -w /app composer install`

Run tests:
`docker run -it --rm -v $PWD:/app -w /app php:8.1-cli vendor/bin/pest`

Run php-cs-fixer on self:
`docker run -it --rm -v $PWD:/app -w /app composer cs-fix`
