<!DOCTYPE html>
<html>
<head>

    <title>Order Successful</title>

    <link rel="icon" type="image/jpeg" href="images/logo.jpeg">

    <style>

        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body{
            background: #f8f8f8;

            display: flex;
            justify-content: center;
            align-items: center;

            min-height: 100vh;
        }

        .success-container{

            background: white;

            width: 90%;
            max-width: 600px;

            padding: 50px;

            border-radius: 15px;

            text-align: center;

            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .success-icon{

            width: 90px;
            height: 90px;

            background: #28a745;

            color: white;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 45px;

            margin: 0 auto 25px;
        }

        h1{
            margin-bottom: 20px;
            color: #111;
        }

        p{
            color: #666;
            line-height: 1.7;
            margin-bottom: 15px;
        }

        .order-message{
            background: #f5f5f5;

            padding: 20px;

            border-radius: 10px;

            margin: 30px 0;
        }

        .continue-btn{

            display: inline-block;

            padding: 15px 30px;

            background: black;
            color: white;

            border-radius: 8px;

            text-decoration: none;

            transition: 0.3s;
        }

        .continue-btn:hover{
            background: #333;
        }

    </style>

</head>

<body>

    <div class="success-container">

        <div class="success-icon">
            ✓
        </div>

        <h1>Order Placed Successfully</h1>

        <p>
            Thank you for shopping with MustBeDelah.
        </p>

        <div class="order-message">

            <p>
                Your order has been received and is being processed.
            </p>

            <p>
                You will receive updates regarding your delivery soon.
            </p>

        </div>

        <a href="shop.php" class="continue-btn">
            Continue Shopping
        </a>

    </div>

</body>
</html>