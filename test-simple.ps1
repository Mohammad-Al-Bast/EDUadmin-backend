# Simple API test for course import
$token = "16|JHPYJvplvPsOfenzhhCFkqHOxR1JVFmtLQ9zrNyf4a248ab5"
$uri = "http://localhost:8000/api/v1/admin/import/courses"

Write-Host "Testing API endpoint..." -ForegroundColor Green
Write-Host "URL: $uri" -ForegroundColor Yellow

# Test with form data
$form = @{
    file = Get-Item -Path "test-file.csv"
    semester = "Fall 2025"
}

$headers = @{
    "Authorization" = "Bearer $token"
}

try {
    $response = Invoke-RestMethod -Uri $uri -Method POST -Headers $headers -Form $form
    Write-Host "Success Response:" -ForegroundColor Green
    $response | ConvertTo-Json -Depth 5
} catch {
    Write-Host "Error Response:" -ForegroundColor Red
    Write-Host "Status Code: $($_.Exception.Response.StatusCode)" -ForegroundColor Red
    Write-Host "Status Description: $($_.Exception.Response.StatusDescription)" -ForegroundColor Red
    
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $responseBody = $reader.ReadToEnd()
        Write-Host "Response Body:" -ForegroundColor Red
        Write-Host $responseBody -ForegroundColor Red
    }
}
