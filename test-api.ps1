# EDU Admin Backend API Testing Script
# Test all APIs with different user types: unverified, verified non-admin, and admin

## Test Configuration
$baseUrl = "http://localhost:8000/api/v1"
$headers = @{ "Content-Type" = "application/json"; "Accept" = "application/json" }

## Test Results
$testResults = @()

function Write-TestResult {
    param($testName, $status, $details)
    $result = @{
        Test = $testName
        Status = $status
        Details = $details
        Time = Get-Date -Format "HH:mm:ss"
    }
    $testResults += $result
    Write-Host "[$($result.Time)] $testName - $status" -ForegroundColor $(if($status -eq "PASS") {"Green"} else {"Red"})
    if ($details) { Write-Host "   Details: $details" -ForegroundColor Gray }
}

function Test-API {
    param($method, $endpoint, $body = $null, $authToken = $null, $expectedStatus = 200)
    
    $requestHeaders = $headers.Clone()
    if ($authToken) {
        $requestHeaders["Authorization"] = "Bearer $authToken"
    }

    try {
        $response = if ($body) {
            Invoke-RestMethod -Uri "$baseUrl$endpoint" -Method $method -Headers $requestHeaders -Body ($body | ConvertTo-Json) -ErrorAction Stop
        } else {
            Invoke-RestMethod -Uri "$baseUrl$endpoint" -Method $method -Headers $requestHeaders -ErrorAction Stop
        }
        return @{ Success = $true; Data = $response; StatusCode = 200 }
    }
    catch {
        $statusCode = if ($_.Exception.Response) { $_.Exception.Response.StatusCode.value__ } else { 0 }
        $errorMessage = if ($_.Exception.Response) { 
            $stream = $_.Exception.Response.GetResponseStream()
            $reader = New-Object System.IO.StreamReader($stream)
            $responseBody = $reader.ReadToEnd()
            try { ($responseBody | ConvertFrom-Json).message } catch { $responseBody }
        } else { $_.Exception.Message }
        
        return @{ Success = $false; Error = $errorMessage; StatusCode = $statusCode }
    }
}

Write-Host "🚀 Starting EDU Admin Backend API Tests..." -ForegroundColor Cyan
Write-Host "Base URL: $baseUrl" -ForegroundColor Yellow

## Step 1: Test Registration (should work for everyone)
Write-Host "`n📝 Testing Registration..." -ForegroundColor Cyan

$registerData = @{
    name = "Test User"
    email = "testuser@example.com"
    password = "password123"
    password_confirmation = "password123"
}

$regResult = Test-API "POST" "/register" $registerData
if ($regResult.Success) {
    Write-TestResult "User Registration" "PASS" "User registered successfully"
    $testUserId = $regResult.Data.user.id
} else {
    Write-TestResult "User Registration" "FAIL" $regResult.Error
}

## Step 2: Test Login with unverified user (should fail)
Write-Host "`n🔐 Testing Login (Unverified User)..." -ForegroundColor Cyan

$loginData = @{
    email = "testuser@example.com"
    password = "password123"
}

$loginResult = Test-API "POST" "/login" $loginData
if (!$loginResult.Success -and $loginResult.StatusCode -eq 403) {
    Write-TestResult "Login (Unverified User)" "PASS" "Correctly blocked unverified user"
} else {
    Write-TestResult "Login (Unverified User)" "FAIL" "Should have blocked unverified user"
}

## Step 3: Create verified non-admin user for testing
Write-Host "`n👤 Creating Test Users..." -ForegroundColor Cyan

# Register admin user
$adminData = @{
    name = "Admin User"
    email = "admin@example.com"
    password = "password123"
    password_confirmation = "password123"
}

$adminRegResult = Test-API "POST" "/register" $adminData
if ($adminRegResult.Success) {
    Write-TestResult "Admin Registration" "PASS" "Admin user registered"
}

# Register regular user
$regularData = @{
    name = "Regular User"
    email = "regular@example.com"
    password = "password123"
    password_confirmation = "password123"
}

$regularRegResult = Test-API "POST" "/register" $regularData
if ($regularRegResult.Success) {
    Write-TestResult "Regular User Registration" "PASS" "Regular user registered"
}

Write-Host "`n⚠️  Manual Step Required:" -ForegroundColor Yellow
Write-Host "Please manually set the following in your database:" -ForegroundColor Yellow
Write-Host "1. Set admin@example.com as admin (is_admin=1, is_verified=1)" -ForegroundColor Yellow
Write-Host "2. Set regular@example.com as verified (is_verified=1)" -ForegroundColor Yellow
Write-Host "3. Keep testuser@example.com as unverified (is_verified=0)" -ForegroundColor Yellow
Write-Host "`nPress Enter to continue after updating the database..."
Read-Host

## Step 4: Test login with different user types
Write-Host "`n🔐 Testing Login (All User Types)..." -ForegroundColor Cyan

# Test admin login
$adminLoginResult = Test-API "POST" "/login" @{ email = "admin@example.com"; password = "password123" }
if ($adminLoginResult.Success) {
    Write-TestResult "Admin Login" "PASS" "Admin logged in successfully"
    $adminToken = $adminLoginResult.Data.token
} else {
    Write-TestResult "Admin Login" "FAIL" $adminLoginResult.Error
}

# Test regular user login
$regularLoginResult = Test-API "POST" "/login" @{ email = "regular@example.com"; password = "password123" }
if ($regularLoginResult.Success) {
    Write-TestResult "Regular User Login" "PASS" "Regular user logged in successfully"
    $regularToken = $regularLoginResult.Data.token
} else {
    Write-TestResult "Regular User Login" "FAIL" $regularLoginResult.Error
}

## Step 5: Test all APIs with different user types
Write-Host "`n🧪 Testing All API Endpoints..." -ForegroundColor Cyan

# Public/Guest endpoints
Write-Host "`n📖 Testing Public Endpoints..." -ForegroundColor Cyan

$publicTests = @(
    @{ name = "Register"; method = "POST"; endpoint = "/register"; body = @{ name = "Test2"; email = "test2@example.com"; password = "password123"; password_confirmation = "password123" } }
    @{ name = "Login"; method = "POST"; endpoint = "/login"; body = @{ email = "admin@example.com"; password = "password123" } }
    @{ name = "Forgot Password"; method = "POST"; endpoint = "/forgot-password"; body = @{ email = "admin@example.com" } }
)

foreach ($test in $publicTests) {
    $result = Test-API $test.method $test.endpoint $test.body
    if ($result.Success -or $result.StatusCode -eq 422) {  # 422 is validation error, which is expected
        Write-TestResult $test.name "PASS" "Public endpoint accessible"
    } else {
        Write-TestResult $test.name "FAIL" $result.Error
    }
}

# Test authenticated endpoints with regular user
Write-Host "`n👤 Testing Regular User Access..." -ForegroundColor Cyan

$regularUserTests = @(
    @{ name = "Get User Info"; method = "GET"; endpoint = "/get-user" }
    @{ name = "Logout"; method = "POST"; endpoint = "/logout" }
    @{ name = "View Courses"; method = "GET"; endpoint = "/courses" }
    @{ name = "View Specific Course"; method = "GET"; endpoint = "/courses/1" }
    @{ name = "View Students (Admin Only)"; method = "GET"; endpoint = "/students"; shouldFail = $true }
    @{ name = "Create Course (Admin Only)"; method = "POST"; endpoint = "/courses"; body = @{ course_name = "Test Course"; course_code = "TC101" }; shouldFail = $true }
    @{ name = "Delete Course (Admin Only)"; method = "DELETE"; endpoint = "/courses/1"; shouldFail = $true }
    @{ name = "Delete All Courses (Admin Only)"; method = "DELETE"; endpoint = "/courses"; shouldFail = $true }
)

foreach ($test in $regularUserTests) {
    $result = Test-API $test.method $test.endpoint $test.body $regularToken
    
    if ($test.shouldFail) {
        if (!$result.Success -and ($result.StatusCode -eq 403 -or $result.StatusCode -eq 401)) {
            Write-TestResult "Regular User: $($test.name)" "PASS" "Correctly blocked non-admin user"
        } else {
            Write-TestResult "Regular User: $($test.name)" "FAIL" "Should have blocked non-admin user"
        }
    } else {
        if ($result.Success -or $result.StatusCode -eq 404) {  # 404 is OK for missing resources
            Write-TestResult "Regular User: $($test.name)" "PASS" "Endpoint accessible to regular user"
        } else {
            Write-TestResult "Regular User: $($test.name)" "FAIL" $result.Error
        }
    }
}

# Test admin endpoints
Write-Host "`n👑 Testing Admin User Access..." -ForegroundColor Cyan

$adminTests = @(
    @{ name = "Get User Info"; method = "GET"; endpoint = "/get-user" }
    @{ name = "View Courses"; method = "GET"; endpoint = "/courses" }
    @{ name = "View Students"; method = "GET"; endpoint = "/students" }
    @{ name = "Create Course"; method = "POST"; endpoint = "/courses"; body = @{ course_name = "Admin Test Course"; course_code = "ATC101"; course_description = "Test course created by admin" } }
    @{ name = "Create Student"; method = "POST"; endpoint = "/students"; body = @{ student_name = "Test Student"; university_id = "12345678"; campus = "Main"; school = "Engineering" } }
    @{ name = "Create User"; method = "POST"; endpoint = "/users"; body = @{ name = "New User"; email = "newuser@example.com"; password = "password123"; is_admin = $false; is_verified = $false } }
    @{ name = "Delete Specific Course"; method = "DELETE"; endpoint = "/courses/1" }
    @{ name = "Delete All Courses"; method = "DELETE"; endpoint = "/courses" }
    @{ name = "Delete Specific Student"; method = "DELETE"; endpoint = "/students/1" }
    @{ name = "Delete All Students"; method = "DELETE"; endpoint = "/students" }
)

foreach ($test in $adminTests) {
    $result = Test-API $test.method $test.endpoint $test.body $adminToken
    
    if ($result.Success -or $result.StatusCode -eq 404 -or $result.StatusCode -eq 422) {
        Write-TestResult "Admin: $($test.name)" "PASS" "Admin endpoint working correctly"
    } else {
        Write-TestResult "Admin: $($test.name)" "FAIL" "$($result.Error) (Status: $($result.StatusCode))"
    }
}

# Test unauthenticated access to protected endpoints
Write-Host "`n🚫 Testing Unauthenticated Access..." -ForegroundColor Cyan

$protectedTests = @(
    @{ name = "Get User Info"; method = "GET"; endpoint = "/get-user" }
    @{ name = "View Courses"; method = "GET"; endpoint = "/courses" }
    @{ name = "Create Course"; method = "POST"; endpoint = "/courses"; body = @{ course_name = "Test"; course_code = "T101" } }
    @{ name = "Delete Course"; method = "DELETE"; endpoint = "/courses/1" }
)

foreach ($test in $protectedTests) {
    $result = Test-API $test.method $test.endpoint $test.body
    
    if (!$result.Success -and $result.StatusCode -eq 401) {
        Write-TestResult "Unauthenticated: $($test.name)" "PASS" "Correctly blocked unauthenticated access"
    } else {
        Write-TestResult "Unauthenticated: $($test.name)" "FAIL" "Should have blocked unauthenticated access"
    }
}

## Summary
Write-Host "`n📊 Test Summary:" -ForegroundColor Cyan
$passCount = ($testResults | Where-Object { $_.Status -eq "PASS" }).Count
$failCount = ($testResults | Where-Object { $_.Status -eq "FAIL" }).Count
$totalCount = $testResults.Count

Write-Host "Total Tests: $totalCount" -ForegroundColor White
Write-Host "Passed: $passCount" -ForegroundColor Green
Write-Host "Failed: $failCount" -ForegroundColor Red

if ($failCount -gt 0) {
    Write-Host "`n❌ Failed Tests:" -ForegroundColor Red
    $testResults | Where-Object { $_.Status -eq "FAIL" } | ForEach-Object {
        Write-Host "  - $($_.Test): $($_.Details)" -ForegroundColor Red
    }
}

Write-Host "`n✅ Testing Complete!" -ForegroundColor Green
