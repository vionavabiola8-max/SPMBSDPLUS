import os
import sys
import uvicorn

if __name__ == "__main__":
    current_dir = os.path.dirname(os.path.abspath(__file__))
    sys.path.append(current_dir)
    print("=" * 60)
    print("Starting SD Plus Darul Ulum ML Microservice on http://127.0.0.1:8000")
    print("XGBoost Regressor & Moving Average Baseline Engine")
    print("=" * 60)
    uvicorn.run("api:app", host="127.0.0.1", port=8000, reload=True, app_dir=current_dir)
