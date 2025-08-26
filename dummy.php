<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSW API Testing Interface</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 30px;
        }

        .section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            border: 1px solid #e9ecef;
        }

        .section h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
        }

        .section h2::before {
            content: "🔗";
            margin-right: 10px;
        }

        .api-endpoint {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .api-endpoint:hover {
            border-color: #3498db;
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.1);
        }

        .endpoint-title {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .endpoint-url {
            background: #2c3e50;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            margin-bottom: 15px;
            word-break: break-all;
        }

        .test-controls {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-pending {
            background: #f39c12;
        }

        .status-success {
            background: #27ae60;
        }

        .status-error {
            background: #e74c3c;
        }

        .response-section {
            grid-column: 1 / -1;
            margin-top: 10px;
        }

        .response-tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }

        .tab {
            padding: 12px 24px;
            background: none;
            border: none;
            cursor: pointer;
            font-weight: 500;
            color: #7f8c8d;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .tab.active {
            color: #3498db;
            border-bottom-color: #3498db;
        }

        .response-content {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 20px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            height: 300px;
            overflow-y: auto;
            white-space: pre-wrap;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .metric-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .metric-value {
            font-size: 2rem;
            font-weight: bold;
            color: #3498db;
            margin-bottom: 5px;
        }

        .metric-label {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .parameter-section {
            margin-top: 15px;
        }

        .parameter-section h4 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .parameter-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }

            .test-controls {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>NSW API Testing Interface</h1>
        <p>การทดสอบการเชื่อมโยงข้อมูล API ตามข้อ 7.4 และ 7.7.1</p>
    </div>

    <div class="main-content">
        <!-- Payment Services API Section -->
        <div class="section">
            <h2>Payment Services API (ข้อ 7.4)</h2>

            <div class="api-endpoint">
                <div class="endpoint-title">
                    <span class="status-indicator status-pending"></span>
                    การรองรับการเชื่อมโยงหรือตรวจสอบข้อมูลค่าบริการ
                </div>
                <div class="endpoint-url">GET /api/v1/services/fees</div>
                <div class="parameter-section">
                    <h4>Parameters:</h4>
                    <input type="text" class="parameter-input" placeholder="Service Code (required)">
                    <input type="text" class="parameter-input" placeholder="Agency Code">
                </div>
                <div class="test-controls">
                    <button class="btn btn-primary" onclick="testAPI('fees')">Test API</button>
                    <button class="btn btn-secondary">View Schema</button>
                </div>
            </div>

            <div class="api-endpoint">
                <div class="endpoint-title">
                    <span class="status-indicator status-pending"></span>
                    ค่าบริการค่าธรรมเนียมหรือค่าใช้จ่ายอื่นๆ
                </div>
                <div class="endpoint-url">GET /api/v1/services/charges</div>
                <div class="parameter-section">
                    <h4>Parameters:</h4>
                    <input type="text" class="parameter-input" placeholder="Transaction ID">
                    <input type="text" class="parameter-input" placeholder="Service Type">
                </div>
                <div class="test-controls">
                    <button class="btn btn-primary" onclick="testAPI('charges')">Test API</button>
                    <button class="btn btn-secondary">View Schema</button>
                </div>
            </div>

            <div class="api-endpoint">
                <div class="endpoint-title">
                    <span class="status-indicator status-pending"></span>
                    การแจ้งผลการรับเงินอิเล็กทรอนิกส์
                </div>
                <div class="endpoint-url">POST /api/v1/payment/notification</div>
                <div class="test-controls">
                    <button class="btn btn-primary" onclick="testAPI('notification')">Test API</button>
                    <button class="btn btn-secondary">View Schema</button>
                </div>
            </div>
        </div>

        <!-- Direct Debit API Section -->
        <div class="section">
            <h2>Direct Debit API (ข้อ 7.7.1)</h2>

            <div class="api-endpoint">
                <div class="endpoint-title">
                    <span class="status-indicator status-pending"></span>
                    การรับการชำระเงินด้วยบัตรเครดิต
                </div>
                <div class="endpoint-url">POST /api/v1/payment/direct-debit</div>
                <div class="parameter-section">
                    <h4>Parameters:</h4>
                    <input type="text" class="parameter-input" placeholder="Account Number">
                    <input type="text" class="parameter-input" placeholder="Amount">
                    <input type="text" class="parameter-input" placeholder="Bank Code">
                </div>
                <div class="test-controls">
                    <button class="btn btn-primary" onclick="testAPI('directDebit')">Test API</button>
                    <button class="btn btn-secondary">View Schema</button>
                </div>
            </div>

            <div class="api-endpoint">
                <div class="endpoint-title">
                    <span class="status-indicator status-pending"></span>
                    การรองรับธุรกรรมรายการค่าบริการ
                </div>
                <div class="endpoint-url">GET /api/v1/transactions/history</div>
                <div class="parameter-section">
                    <h4>Parameters:</h4>
                    <input type="date" class="parameter-input" placeholder="Start Date">
                    <input type="date" class="parameter-input" placeholder="End Date">
                </div>
                <div class="test-controls">
                    <button class="btn btn-primary" onclick="testAPI('transactions')">Test API</button>
                    <button class="btn btn-secondary">View Schema</button>
                </div>
            </div>

            <div class="api-endpoint">
                <div class="endpoint-title">
                    <span class="status-indicator status-pending"></span>
                    จัดทำ File Layout ของค่าสั่ง Direct Debit
                </div>
                <div class="endpoint-url">GET /api/v1/files/direct-debit-layout</div>
                <div class="test-controls">
                    <button class="btn btn-primary" onclick="testAPI('fileLayout')">Test API</button>
                    <button class="btn btn-secondary">Download Sample</button>
                </div>
            </div>
        </div>

        <!-- Response Section -->
        <div class="section response-section">
            <h2>Response & Monitoring</h2>

            <div class="response-tabs">
                <button class="tab active" onclick="switchTab('response')">API Response</button>
                <button class="tab" onclick="switchTab('logs')">Logs</button>
                <button class="tab" onclick="switchTab('errors')">Errors</button>
            </div>

            <div class="response-content" id="responseContent">
                {
                "status": "ready",
                "message": "Select an API endpoint to test",
                "timestamp": "2025-01-23T10:30:00Z"
                }
            </div>

            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-value" id="totalTests">0</div>
                    <div class="metric-label">Total Tests</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value" id="successRate">0%</div>
                    <div class="metric-label">Success Rate</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value" id="avgResponse">0ms</div>
                    <div class="metric-label">Avg Response Time</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value" id="lastTest">Never</div>
                    <div class="metric-label">Last Test</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let testCount = 0;
    let successCount = 0;
    let totalResponseTime = 0;

    function testAPI(endpoint) {
        const startTime = Date.now();
        testCount++;

        // Update status indicator
        const indicators = document.querySelectorAll('.status-indicator');
        indicators.forEach(indicator => {
            if (indicator.parentElement.textContent.includes(getEndpointTitle(endpoint))) {
                indicator.className = 'status-indicator status-pending';
            }
        });

        // Simulate API call
        setTimeout(() => {
            const responseTime = Date.now() - startTime;
            const isSuccess = Math.random() > 0.2; // 80% success rate for demo

            if (isSuccess) {
                successCount++;
                updateStatusIndicator(endpoint, 'success');
                showResponse(generateSuccessResponse(endpoint));
            } else {
                updateStatusIndicator(endpoint, 'error');
                showResponse(generateErrorResponse(endpoint));
            }

            totalResponseTime += responseTime;
            updateMetrics();
        }, Math.random() * 2000 + 500);
    }

    function getEndpointTitle(endpoint) {
        const titles = {
            'fees': 'การรองรับการเชื่อมโยงหรือตรวจสอบข้อมูลค่าบริการ',
            'charges': 'ค่าบริการค่าธรรมเนียมหรือค่าใช้จ่ายอื่นๆ',
            'notification': 'การแจ้งผลการรับเงินอิเล็กทรอนิกส์',
            'directDebit': 'การรับการชำระเงินด้วยบัตรเครดิต',
            'transactions': 'การรองรับธุรกรรมรายการค่าบริการ',
            'fileLayout': 'จัดทำ File Layout ของค่าสั่ง Direct Debit'
        };
        return titles[endpoint] || endpoint;
    }

    function updateStatusIndicator(endpoint, status) {
        const indicators = document.querySelectorAll('.status-indicator');
        indicators.forEach(indicator => {
            if (indicator.parentElement.textContent.includes(getEndpointTitle(endpoint))) {
                indicator.className = `status-indicator status-${status}`;
            }
        });
    }

    function generateSuccessResponse(endpoint) {
        const responses = {
            'fees': {
                "status": "success",
                "data": {
                    "serviceCode": "NSW001",
                    "serviceName": "ใบอนุญาตการขายอาหาร",
                    "baseFee": 500,
                    "processingFee": 50,
                    "totalFee": 550,
                    "currency": "THB"
                },
                "timestamp": new Date().toISOString()
            },
            'charges': {
                "status": "success",
                "data": {
                    "transactionId": "TXN" + Date.now(),
                    "charges": [
                        {"type": "service_fee", "amount": 500},
                        {"type": "processing_fee", "amount": 50},
                        {"type": "vat", "amount": 38.5}
                    ],
                    "totalAmount": 588.5
                }
            },
            'directDebit': {
                "status": "success",
                "data": {
                    "paymentId": "PAY" + Date.now(),
                    "amount": 1000,
                    "status": "completed",
                    "accountNumber": "****1234",
                    "bankCode": "SCB"
                }
            }
        };

        return JSON.stringify(responses[endpoint] || {"status": "success", "message": "API test completed"}, null, 2);
    }

    function generateErrorResponse(endpoint) {
        return JSON.stringify({
            "status": "error",
            "error": {
                "code": "API_ERROR",
                "message": "Connection timeout",
                "details": "Unable to connect to NSW payment gateway"
            },
            "timestamp": new Date().toISOString()
        }, null, 2);
    }

    function showResponse(response) {
        document.getElementById('responseContent').textContent = response;
    }

    function updateMetrics() {
        document.getElementById('totalTests').textContent = testCount;
        document.getElementById('successRate').textContent = Math.round((successCount / testCount) * 100) + '%';
        document.getElementById('avgResponse').textContent = Math.round(totalResponseTime / testCount) + 'ms';
        document.getElementById('lastTest').textContent = new Date().toLocaleTimeString('th-TH');
    }

    function switchTab(tabName) {
        // Update tab appearance
        document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
        event.target.classList.add('active');

        // Update content based on tab
        const content = {
            'response': '{\n  "status": "ready",\n  "message": "Select an API endpoint to test",\n  "timestamp": "' + new Date().toISOString() + '"\n}',
            'logs': '[' + new Date().toISOString() + '] System initialized\n[' + new Date().toISOString() + '] API endpoints loaded\n[' + new Date().toISOString() + '] Ready for testing',
            'errors': 'No errors recorded'
        };

        document.getElementById('responseContent').textContent = content[tabName];
    }
</script>
</body>
</html>