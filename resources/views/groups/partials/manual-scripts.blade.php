<script>
document.addEventListener('DOMContentLoaded', function() {
    const memberPositions = document.getElementById('member-positions');
    const saveButton = document.getElementById('save-positions');

    if (memberPositions && saveButton) {
        // Make the list sortable
        new Sortable(memberPositions, {
            animation: 150,
            ghostClass: 'opacity-50',
            onEnd: function(evt) {
                updatePositionNumbers();
            }
        });

        function updatePositionNumbers() {
            const items = memberPositions.children;
            for (let i = 0; i < items.length; i++) {
                const positionBadge = items[i].querySelector('.w-8.h-8');
                const positionText = items[i].querySelector('.text-sm.text-gray-500');
                positionBadge.textContent = i + 1;
                positionText.textContent = `Position #${i + 1}`;
                items[i].setAttribute('data-position', i + 1);
            }
        }

        saveButton.addEventListener('click', function() {
            const positions = [];
            const items = memberPositions.children;

            for (let i = 0; i < items.length; i++) {
                positions.push({
                    member_uuid: items[i].getAttribute('data-member-uuid'),
                    position: i + 1
                });
            }

            // Save positions via AJAX
            positions.forEach(item => {
                fetch(`{{ route('groups.update-member-position', $group->uuid) }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(item)
                });
            });

            // Show success message
            alert("{{ __('general.member_position_updated') }}");
        });
    }
});
</script>

<!-- Include SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>