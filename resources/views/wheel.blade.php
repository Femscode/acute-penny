<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Card Flip Reveal Test</title>
<style>
    body {
        font-family: Arial, sans-serif;
        text-align: center;
        background: #f4f4f4;
        padding-top: 50px;
    }
    h2 {
        color: #333;
        margin-bottom: 20px;
    }
    .card-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        max-width: 600px;
        margin: 20px auto;
        gap: 15px;
    }
    .card {
        width: 80px;
        height: 120px;
        perspective: 1000px;
        cursor: pointer;
    }
    .card-inner {
        position: relative;
        width: 100%;
        height: 100%;
        transition: transform 0.8s;
        transform-style: preserve-3d;
    }
    .card.flipped .card-inner {
        transform: rotateY(180deg);
    }
    .card-front, .card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: bold;
        color: #fff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .card-front {
        background: #2c3e50;
    }
    .card-back {
        background: #27ae60;
        transform: rotateY(180deg);
    }
    #reveal-btn {
        margin-top: 20px;
        padding: 12px 20px;
        font-size: 18px;
        color: #fff;
        background: linear-gradient(to right, #6a11cb, #2575fc);
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.3s;
    }
    #reveal-btn:hover {
        transform: scale(1.05);
        opacity: 0.9;
    }
</style>
</head>
<body>

<h2>🎲 Discover Your Turn Position</h2>
<div class="card-container" id="card-container">
    <!-- Cards will be generated here -->
</div>

<button id="reveal-btn">Reveal My Position</button>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const memberCount = 12; // change to test different group sizes
    const targetPosition = 5; // ✅ mock assigned position (replace with backend data later)

    const cardContainer = document.getElementById("card-container");

    // Generate cards
    for (let i = 1; i <= memberCount; i++) {
        const card = document.createElement("div");
        card.classList.add("card");
        card.innerHTML = `
            <div class="card-inner">
                <div class="card-front">?</div>
                <div class="card-back">${i}</div>
            </div>
        `;
        cardContainer.appendChild(card);
    }

    document.getElementById("reveal-btn").addEventListener("click", function() {
        const cards = document.querySelectorAll(".card");
        cards.forEach((card, index) => {
            if (index + 1 === targetPosition) {
                setTimeout(() => {
                    card.classList.add("flipped");
                }, 500);
            }
        });

        this.disabled = true;
        this.textContent = "Revealed!";
        this.style.opacity = "0.7";
    });
});
</script>

</body>
</html>
