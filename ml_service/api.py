"""
FastAPI Microservice for SD Plus Darul Ulum SPMB Prediction Engine
Exposes RESTful endpoints for:
- XGBoost Training & Retraining
- Model Evaluation (XGBoost vs Moving Average comparison)
- Feature Importance
- Forecasting & EWS Recommendations
"""

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
import os
import sys

# Add current dir to path
sys.path.append(os.path.dirname(os.path.abspath(__file__)))
from ml_engine import run_pipeline, connect_db

app = FastAPI(
    title="SD Plus Darul Ulum - ML Prediction API",
    description="Machine Learning Service with XGBoost & Moving Average Baseline for Student Enrollment Forecasting",
    version="1.0.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

@app.get("/")
def root():
    return {
        "service": "SD Plus Darul Ulum ML API",
        "algorithm": "XGBoost Regressor & Moving Average Baseline",
        "status": "online"
    }

@app.post("/api/train")
def train_model():
    try:
        result = run_pipeline(save_to_db=True)
        return result
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/api/evaluation")
def get_evaluation():
    try:
        conn = connect_db()
        cursor = conn.cursor()
        cursor.execute("""
            SELECT id_evaluasi, tanggal_training, algoritma, nilai_rmse, nilai_mape, catatan
            FROM tb_model_evaluation
            ORDER BY id_evaluasi DESC LIMIT 10
        """)
        rows = [dict(r) for r in cursor.fetchall()]
        conn.close()
        return {"data": rows}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/api/forecast")
def get_forecast():
    try:
        conn = connect_db()
        cursor = conn.cursor()
        cursor.execute("""
            SELECT id_prediksi, tahun_proyeksi, estimasi_jumlah_siswa, status_kapasitas, rekomendasi_sistem, detail_komparasi
            FROM tb_hasil_prediksi
            ORDER BY id_prediksi ASC
        """)
        rows = [dict(r) for r in cursor.fetchall()]
        conn.close()
        return {"data": rows}
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("api:app", host="127.0.0.1", port=8000, reload=True)
