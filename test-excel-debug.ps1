# Test the specific Excel file case that's failing
Write-Host "Testing Excel file import with semester 'summer'..." -ForegroundColor Green

# Login first
$loginResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/v1/auth/login" -Method POST -Headers @{"Content-Type"="application/json"} -Body '{"email":"admin@test.com","password":"password"}'
$token = $loginResponse.token
Write-Host "Token: $token" -ForegroundColor Yellow

# Create a test Excel file to simulate the issue
$testData = @"
Code,Course,Instructor,Section,Credits,Room,Schedule,Days,Time,School
CS101,Summer Programming,Dr. Smith,A,3,Room 101,MWF,Monday Wednesday Friday,9:00-10:00,Engineering
CS102,Summer Algorithms,Dr. Jones,B,4,Room 102,TTh,Tuesday Thursday,10:00-11:30,Engineering
"@

Set-Content -Path "summer-test.csv" -Value $testData

# Test with the exact same parameters as the frontend
$form = @{
    file = Get-Item -Path "summer-test.csv"
    semester = "summer"  # Lowercase like in the error
}

$headers = @{
    "Authorization" = "Bearer $token"
}

try {
    Write-Host "Sending request to: http://localhost:8000/api/v1/admin/import/courses" -ForegroundColor Cyan
    Write-Host "Semester: summer" -ForegroundColor Cyan
    Write-Host "File: summer-test.csv" -ForegroundColor Cyan
    
    $response = Invoke-WebRequest -Uri "http://localhost:8000/api/v1/admin/import/courses" -Method POST -Headers $headers -Form $form
    
    Write-Host "Success Response:" -ForegroundColor Green
    Write-Host $response.Content -ForegroundColor White
} catch {
    Write-Host "Error Response:" -ForegroundColor Red
    Write-Host "Status Code: $($_.Exception.Response.StatusCode.value__)" -ForegroundColor Red
    
    try {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $responseBody = $reader.ReadToEnd()
        Write-Host "Response Body:" -ForegroundColor Red
        Write-Host $responseBody -ForegroundColor White
        
        # Try to parse as JSON for better formatting
        try {
            $errorJson = $responseBody | ConvertFrom-Json
            Write-Host "`nParsed Error Details:" -ForegroundColor Yellow
            $errorJson | ConvertTo-Json -Depth 3
        } catch {
            Write-Host "Could not parse response as JSON" -ForegroundColor Gray
        }
    } catch {
        Write-Host "Could not read response body" -ForegroundColor Red
    }
}

# Clean up
Remove-Item "summer-test.csv" -Force -ErrorAction SilentlyContinue
