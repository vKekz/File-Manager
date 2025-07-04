Set-Location -Path $PSScriptRoot

$tag = "shelfy-api"
$context = "../../../"
$command = "docker build --tag $tag --file ../Dockerfile $context"

Write-Host "Running Docker build for image: $tag..."
Write-Host "> $command"
Invoke-Expression $command

if ($LASTEXITCODE -ne 0) {
    Write-Error "Docker build failed with exit code: $LASTEXITCODE"
    exit $LASTEXITCODE
}

Write-Host "Docker build completed successfully"