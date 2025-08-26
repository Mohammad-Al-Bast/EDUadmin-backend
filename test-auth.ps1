# Test course import with proper authentication
Write-Host "Step 1: Logging in..." -ForegroundColor Green

$loginResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/v1/auth/login" -Method POST -Headers @{"Content-Type"="application/json"} -Body '{"email":"admin@test.com","password":"password"}'

$token = $loginResponse.token
Write-Host "Token received: $token" -ForegroundColor Yellow

Write-Host "`nStep 2: Testing course import..." -ForegroundColor Green

$form = @{
    file = Get-Item -Path "test-file.csv"
    semester = "Fall 2025"
}

$headers = @{
    "Authorization" = "Bearer $token"
}

try {
    # Use multipart form data manually
    $boundary = [System.Guid]::NewGuid().ToString()
    $fileContent = [System.IO.File]::ReadAllBytes("test-file.csv")
    $fileName = "test-file.csv"
    
    $bodyLines = @(
        "--$boundary",
        'Content-Disposition: form-data; name="semester"',
        '',
        'Fall 2025',
        "--$boundary",
        'Content-Disposition: form-data; name="file"; filename="' + $fileName + '"',
        'Content-Type: text/csv',
        '',
        [System.Text.Encoding]::UTF8.GetString($fileContent),
        "--$boundary--"
    )
    
    $body = $bodyLines -join "`r`n"
    
    $headers["Content-Type"] = "multipart/form-data; boundary=$boundary"
    
    $response = Invoke-RestMethod -Uri "http://localhost:8000/api/v1/admin/import/courses" -Method POST -Headers $headers -Body $body
    Write-Host "Success Response:" -ForegroundColor Green
    $response | ConvertTo-Json -Depth 5
} catch {
    Write-Host "Error Response:" -ForegroundColor Red
    Write-Host "Exception: $($_.Exception.Message)" -ForegroundColor Red
    
    if ($_.Exception.Response) {
        Write-Host "Status Code: $($_.Exception.Response.StatusCode.value__)" -ForegroundColor Red
        try {
            $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
            $responseBody = $reader.ReadToEnd()
            Write-Host "Response Body:" -ForegroundColor Red
            Write-Host $responseBody -ForegroundColor White
        } catch {
            Write-Host "Could not read response body" -ForegroundColor Red
        }
    }
}
