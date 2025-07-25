<script>
document.addEventListener('DOMContentLoaded', function() {
    // Member wheel elements
    const memberSpinWheel = document.getElementById('member-spin-wheel');
    const memberSpinBtn = document.getElementById('member-spin-btn');
    const memberSpinResult = document.getElementById('member-spin-result');
    const memberResultText = document.getElementById('member-result-text');
    
    let availablePositions = [];
    let wheelSVG = null;
    let segmentAngle = 0;
    let selectedPositionIndex = null;

    function fetchAvailablePositionsAndCreateWheel() {
        fetch(`{{ route('groups.available-positions', $group->uuid) }}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    availablePositions = data.positions;
                    if (availablePositions.length > 0) {
                        createWheelWithAvailablePositions(availablePositions);
                    } else {
                        memberSpinBtn.disabled = true;
                        memberSpinBtn.textContent = '{{ __('general.no_positions_available') }}';
                    }
                } else {
                    console.error('Failed to fetch available positions:', data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching positions:', error);
            });
    }

    function createWheelWithAvailablePositions(positions) {
        if (!memberSpinWheel) return;

        const colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
            '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E9',
            '#F8C471', '#82E0AA', '#AED6F1', '#D7BDE2', '#F9E79F'
        ];

        memberSpinWheel.innerHTML = '';

        const wheelContainer = document.createElement('div');
        wheelContainer.style.position = 'relative';
        wheelContainer.style.width = '320px';
        wheelContainer.style.height = '320px';
        wheelContainer.style.margin = '0 auto';

        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('width', '320');
        svg.setAttribute('height', '320');
        svg.setAttribute('viewBox', '0 0 320 320');
        svg.style.borderRadius = '50%';
        svg.style.boxShadow = '0 8px 32px rgba(0,0,0,0.3)';
        svg.style.transition = 'transform 4s cubic-bezier(0.23, 1, 0.32, 1)';

        const centerX = 160;
        const centerY = 160;
        const radius = 150;

        segmentAngle = 360 / positions.length;

        const outerRing = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        outerRing.setAttribute('cx', centerX);
        outerRing.setAttribute('cy', centerY);
        outerRing.setAttribute('r', radius + 5);
        outerRing.setAttribute('fill', '#2c3e50');
        svg.appendChild(outerRing);

        for (let i = 0; i < positions.length; i++) {
            // ✅ Adjusted so 0° starts at 12 o'clock
            const startAngle = (i * segmentAngle) * Math.PI / 180 - Math.PI / 2;
            const endAngle = ((i + 1) * segmentAngle) * Math.PI / 180 - Math.PI / 2;

            const x1 = centerX + radius * Math.cos(startAngle);
            const y1 = centerY + radius * Math.sin(startAngle);
            const x2 = centerX + radius * Math.cos(endAngle);
            const y2 = centerY + radius * Math.sin(endAngle);

            const largeArcFlag = segmentAngle > 180 ? 1 : 0;

            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            const pathData = [
                `M ${centerX} ${centerY}`,
                `L ${x1} ${y1}`,
                `A ${radius} ${radius} 0 ${largeArcFlag} 1 ${x2} ${y2}`,
                'Z'
            ].join(' ');

            path.setAttribute('d', pathData);
            path.setAttribute('fill', colors[i % colors.length]);
            path.setAttribute('stroke', '#ffffff');
            path.setAttribute('stroke-width', '3');
            path.style.filter = 'drop-shadow(0 2px 4px rgba(0,0,0,0.2))';

            svg.appendChild(path);

            // ✅ Text placement also adjusted for 12 o'clock reference
            const textAngle = ((i + 0.5) * segmentAngle - 90) * Math.PI / 180;
            const textRadius = radius * 0.75;
            const textX = centerX + textRadius * Math.cos(textAngle);
            const textY = centerY + textRadius * Math.sin(textAngle);

            const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            text.setAttribute('x', textX);
            text.setAttribute('y', textY);
            text.setAttribute('text-anchor', 'middle');
            text.setAttribute('dominant-baseline', 'middle');
            text.setAttribute('fill', '#ffffff');
            text.setAttribute('font-size', positions.length > 10 ? '18' : '24');
            text.setAttribute('font-weight', 'bold');
            text.setAttribute('font-family', 'Arial, sans-serif');
            text.textContent = positions[i];

            svg.appendChild(text);
        }

        const centerCircle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        centerCircle.setAttribute('cx', centerX);
        centerCircle.setAttribute('cy', centerY);
        centerCircle.setAttribute('r', '25');
        centerCircle.setAttribute('fill', '#2c3e50');
        centerCircle.setAttribute('stroke', '#ffffff');
        centerCircle.setAttribute('stroke-width', '3');
        svg.appendChild(centerCircle);

        wheelContainer.appendChild(svg);

        const pointer = document.createElement('div');
        pointer.style.position = 'absolute';
        pointer.style.top = '-10px';
        pointer.style.left = '50%';
        pointer.style.transform = 'translateX(-50%)';
        pointer.style.width = '0';
        pointer.style.height = '0';
        pointer.style.borderLeft = '15px solid transparent';
        pointer.style.borderRight = '15px solid transparent';
        pointer.style.borderTop = '30px solid #e74c3c';
        pointer.style.zIndex = '10';
        pointer.style.filter = 'drop-shadow(0 2px 4px rgba(0,0,0,0.3))';

        wheelContainer.appendChild(pointer);
        memberSpinWheel.appendChild(wheelContainer);

        wheelSVG = svg;
    }

   
    function oldcalculateWinningPosition() {
    selectedPositionIndex = Math.floor(Math.random() * availablePositions.length);

    // Calculate the angle for the center of the selected segment
    // Since segments start at 12 o'clock (top), segment 0 is at 0°
    const targetSegmentCenter = selectedPositionIndex * segmentAngle + (segmentAngle / 2);
    
    // Add multiple full rotations for visual effect
    const baseRotations = 5 + Math.random() * 3;
    
    // Calculate final rotation so the pointer lands on the selected segment
    // We need to rotate the wheel so that the selected segment ends up at the top (0°)
    const finalRotation = (baseRotations * 360) - targetSegmentCenter;

    return {
        rotation: finalRotation,
        selectedPosition: availablePositions[selectedPositionIndex],
        selectedIndex: selectedPositionIndex
    };
}
    if (memberSpinBtn) {
        memberSpinBtn.addEventListener('click', function() {
            if (memberSpinBtn.disabled) return;

            memberSpinBtn.disabled = true;
            memberSpinBtn.textContent = '{{ __('general.spinning') }}...';

            const spinResult = calculateWinningPosition();

            wheelSVG.style.transform = `rotate(${spinResult.rotation}deg)`;
            wheelSVG.style.filter = 'brightness(1.2) saturate(1.3)';

            setTimeout(() => {
                fetch("{{ route('groups.spin-wheel', $group->uuid) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ selected_position: spinResult.selectedPosition })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        wheelSVG.style.filter = 'none';
                        memberResultText.textContent = `{{ __('general.your_turn_position_is') }} #${data.position}!`;
                        memberSpinResult.classList.remove('hidden');
                        memberSpinResult.style.animation = 'fadeInBounce 0.6s ease-out';
                        
                        memberSpinBtn.style.transition = 'opacity 0.3s ease';
                        memberSpinBtn.style.opacity = '0';
                        setTimeout(() => {
                            memberSpinBtn.style.display = 'none';
                        }, 300);

                        createConfetti();

                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    } else {
                        alert(data.message);
                        memberSpinBtn.disabled = false;
                        memberSpinBtn.textContent = '🎲 {{ __('general.discover_my_position') }}';
                        wheelSVG.style.transform = 'rotate(0deg)';
                        wheelSVG.style.filter = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('{{ __('general.error_occurred') }}');
                    memberSpinBtn.disabled = false;
                    memberSpinBtn.textContent = '🎲 {{ __('general.discover_my_position') }}';
                    wheelSVG.style.transform = 'rotate(0deg)';
                    wheelSVG.style.filter = 'none';
                });
            }, 4200);
        });
    }
// ... existing code ...

function calculateWinningPosition() {
    selectedPositionIndex = Math.floor(Math.random() * availablePositions.length);
    const selectedPosition = availablePositions[selectedPositionIndex];

    // Calculate the angle for the center of the selected segment
    // Since segments start at 12 o'clock (top), segment 0 is at 0°
    const targetSegmentCenter = selectedPositionIndex * segmentAngle + (segmentAngle / 2);
    
    // Add multiple full rotations for visual effect
    const baseRotations = 5 + Math.random() * 3;
    
    // Calculate final rotation so the pointer lands on the selected segment
    // We need to rotate the wheel so that the selected segment ends up at the top (0°)
    const finalRotation = (baseRotations * 360) - targetSegmentCenter;

    return {
        rotation: finalRotation,
        selectedPosition: selectedPosition,
        selectedIndex: selectedPositionIndex
    };
}

if (memberSpinBtn) {
    memberSpinBtn.addEventListener('click', function() {
        if (memberSpinBtn.disabled) return;

        memberSpinBtn.disabled = true;
        memberSpinBtn.textContent = '{{ __('general.spinning') }}...';

        const spinResult = calculateWinningPosition();

        // First send the position to the server before animating
        fetch("{{ route('groups.spin-wheel', $group->uuid) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ selected_position: spinResult.selectedPosition })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Only start the wheel animation after server confirms
                wheelSVG.style.transform = `rotate(${spinResult.rotation}deg)`;
                wheelSVG.style.filter = 'brightness(1.2) saturate(1.3)';

                setTimeout(() => {
                    wheelSVG.style.filter = 'none';
                    memberResultText.textContent = `{{ __('general.your_turn_position_is') }} #${data.position}!`;
                    memberSpinResult.classList.remove('hidden');
                    memberSpinResult.style.animation = 'fadeInBounce 0.6s ease-out';
                    
                    memberSpinBtn.style.transition = 'opacity 0.3s ease';
                    memberSpinBtn.style.opacity = '0';
                    setTimeout(() => {
                        memberSpinBtn.style.display = 'none';
                    }, 300);

                    createConfetti();

                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                }, 4200); // Wait for wheel animation to complete
            } else {
                alert(data.message);
                memberSpinBtn.disabled = false;
                memberSpinBtn.textContent = '🎲 {{ __('general.discover_my_position') }}';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __('general.error_occurred') }}');
            memberSpinBtn.disabled = false;
            memberSpinBtn.textContent = '🎲 {{ __('general.discover_my_position') }}';
        });
    });
}



    function createConfetti() {
        const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffeaa7'];
        const confettiContainer = document.createElement('div');
        confettiContainer.style.position = 'fixed';
        confettiContainer.style.top = '0';
        confettiContainer.style.left = '0';
        confettiContainer.style.width = '100%';
        confettiContainer.style.height = '100%';
        confettiContainer.style.pointerEvents = 'none';
        confettiContainer.style.zIndex = '9999';
        document.body.appendChild(confettiContainer);

        for (let i = 0; i < 50; i++) {
            const confetti = document.createElement('div');
            confetti.style.position = 'absolute';
            confetti.style.width = '10px';
            confetti.style.height = '10px';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.top = '-10px';
            confetti.style.borderRadius = '50%';
            confetti.style.animation = `confettiFall ${2 + Math.random() * 3}s linear forwards`;
            confettiContainer.appendChild(confetti);
        }

        setTimeout(() => {
            document.body.removeChild(confettiContainer);
        }, 5000);
    }

    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInBounce {
            0% { opacity: 0; transform: scale(0.3) translateY(-20px); }
            50% { opacity: 1; transform: scale(1.1) translateY(-10px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        @keyframes confettiFall {
            0% { transform: translateY(-10px) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
        }
        #member-spin-wheel { position: relative; }
        #member-spin-wheel svg { transition: transform 4s cubic-bezier(0.23, 1, 0.32, 1); }
    `;
    document.head.appendChild(style);

    fetchAvailablePositionsAndCreateWheel();
});
</script>
