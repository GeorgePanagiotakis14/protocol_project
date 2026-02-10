<x-app-layout>



    <style>

        /* Background εικόνα για όλη τη σελίδα */
        .home-wrapper {
            padding: 40px;
            min-height: 100vh;
            background-image: url('/images/background-protokols.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Λογότυπο βιβλιοθήκης κάτω δεξιά */
        .library-logo {
            position: fixed;
            bottom: 10px;
            right: 10px;
            width: 120px;
            height: auto;
            opacity: 0.85;
            z-index: 900;
        }

        /* Τίτλος */
        .home-title {
            margin-bottom: 40px;
            padding: 25px 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            color: #222;
            text-align: center;
            max-width: 800px;
            width: 100%;
        }

        .home-title h1 {
            font-size: 34px;
            margin-bottom: 8px;
        }

        .home-title p {
            font-size: 16px;
            color: #555;
            line-height: 1.5;
        }

        /* ΚΑΡΤΕΣ: 3 + 2 */
        .home-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .home-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            text-decoration: none;
            color: #222;
            transition: all 0.25s ease;
            border-left: 6px solid #2c3e50;
            width: 100%;
        }


        .home-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        }

        .home-card h3 {
            font-size: 20px;
            margin-bottom: 12px;
        }

        .home-card p {
            font-size: 14px;
            color: #555;
            line-height: 1.6;
        }

        /* Footer */
        .home-footer {
            margin-top: 50px;
            font-size: 13px;
            color: #888;
            text-align: center;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .home-cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .home-card:nth-child(4),
            .home-card:nth-child(5) {
                grid-column: auto;
                justify-self: stretch;
            }
        }

        @media (max-width: 600px) {
            .home-cards {
                grid-template-columns: 1fr;
            }
        }

    </style>


    <div class="home-wrapper">

        <div class="home-title">
            <h1>Σύστημα Πρωτοκόλλου</h1>
            <p>Κεντρική διαχείριση εισερχόμενων και εξερχόμενων εγγράφων</p>
        </div>

        <div class="home-cards">

            <a href="{{ route('documents.create') }}" class="home-card">
                <h3>Νέα Καταχώρηση</h3>
                <p>Άμεση καταχώρηση νέου εισερχόμενου ή εξερχόμενου εγγράφου στο σύστημα.</p>
            </a>

            <a href="{{ route('incoming.index') }}" class="home-card">
                <h3>Εισερχόμενα Έγγραφα</h3>
                <p>Προβολή, αναζήτηση και διαχείριση όλων των εισερχόμενων εγγράφων του οργανισμού.</p>
            </a>

            <a href="{{ route('outgoing.index') }}" class="home-card">
                <h3>Εξερχόμενα Έγγραφα</h3>
                <p>Παρακολούθηση και καταχώρηση εξερχόμενων εγγράφων με πλήρη στοιχεία πρωτοκόλλου.</p>
            </a>

            <a href="{{ route('documents.common') }}" class="home-card">
                <h3>Κοινά Έγγραφα</h3>
                <p>Έγγραφα που σχετίζονται τόσο με εισερχόμενα όσο και με εξερχόμενα.</p>
            </a>

            <a href="{{ route('documents.all') }}" class="home-card">
                <h3>Όλα τα πρωτόκολλα</h3>
                <p>Προβολή όλων των εγγράφων: εισερχόμενα, εξερχόμενα και κοινά.</p>
            </a>

            <a href="{{ route('attachments.tree') }}" class="home-card">
            <h3>Επισυναπτόμενα</h3>
            <p>Προβολή όλων των επισυναπτόμενων σε μορφή δέντρου.</p>
            </a>


        </div>



    </div>

    <img src="/images/library-sparta-logo.png"
         alt="Δημόσια Κεντρική Βιβλιοθήκη Σπάρτης"
         class="library-logo">

</x-app-layout>
