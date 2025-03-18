<tr>
    <td colspan="100%">
        <div class="bg-gray-100 p-2 flex justify-end">
            <div class="w-1/2 text-center"> <!-- Membatasi lebar agar tidak terlalu ke kanan -->
                <strong>Subtotal:</strong> Rp {{ number_format($total ?? 0, 0, ',', '.') }}
            </div>
        </div>
    </td>
</tr>